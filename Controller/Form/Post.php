<?php
/*
 * Copyright (c) 2020 Zengliwei
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Common\Forms\Controller\Form;

use Common\Forms\Model\Form;
use Common\Forms\Model\Form\PostRecord;
use Common\Forms\Model\Form\PostRecordFactory;
use Common\Forms\Model\FormFactory;
use Common\Forms\Model\ResourceModel\Form as ResourceForm;
use Common\Forms\Model\ResourceModel\Form\PostRecord as ResourceRecord;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Url\DecoderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @package Common\Forms
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Post extends Action implements HttpPostActionInterface
{
    /**
     * @var FormFactory
     */
    private FormFactory $formFactory;

    /**
     * @var PostRecordFactory
     */
    protected PostRecordFactory $postRecordFactory;

    /**
     * @var ResourceForm
     */
    private ResourceForm $resourceForm;

    /**
     * @var ResourceRecord
     */
    protected ResourceRecord $resourceRecord;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var SenderResolverInterface
     */
    protected SenderResolverInterface $senderResolver;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var DecoderInterface
     */
    protected DecoderInterface $urlDecoder;

    /**
     * @var TransportBuilder
     */
    protected TransportBuilder $transportBuilder;

    /**
     * @param DecoderInterface        $urlDecoder
     * @param FormFactory             $formFactory
     * @param PostRecordFactory       $postRecordFactory
     * @param ResourceForm            $resourceForm
     * @param ResourceRecord          $resourceRecord
     * @param ScopeConfigInterface    $scopeConfig
     * @param SenderResolverInterface $senderResolver
     * @param StoreManagerInterface   $storeManager
     * @param TransportBuilder        $transportBuilder
     * @param Context                 $context
     */
    public function __construct(
        DecoderInterface $urlDecoder,
        FormFactory $formFactory,
        PostRecordFactory $postRecordFactory,
        ResourceForm $resourceForm,
        ResourceRecord $resourceRecord,
        ScopeConfigInterface $scopeConfig,
        SenderResolverInterface $senderResolver,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        Context $context
    ) {
        $this->formFactory = $formFactory;
        $this->postRecordFactory = $postRecordFactory;
        $this->resourceForm = $resourceForm;
        $this->resourceRecord = $resourceRecord;
        $this->urlDecoder = $urlDecoder;
        $this->scopeConfig = $scopeConfig;
        $this->senderResolver = $senderResolver;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /* @var $resultRedirect Redirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $redirectUrl = $this->urlDecoder->decode(
            $this->getRequest()->getParam(ActionInterface::PARAM_NAME_URL_ENCODED)
        );

        /* @var $form Form */
        $formId = $this->getRequest()->getParam('id');
        $form = $this->formFactory->create();
        $this->resourceForm->load($form, $formId);
        if (!$form->getId()) {
            return $resultRedirect->setUrl($redirectUrl);
        }

        $fromName = $form->getDataByKey(Form::FIELD_FROM_NAME);
        $fromEmail = $form->getDataByKey(Form::FIELD_FROM_NAME);
        $toName = $form->getDataByKey(Form::FIELD_FROM_NAME);
        $toEmail = $form->getDataByKey(Form::FIELD_FROM_NAME);

        $storeId = $this->storeManager->getStore()->getId();
        if (!$fromEmail) {
            $sender = $this->senderResolver->resolve(
                $this->scopeConfig->getValue(
                    'contact/forms/sender_email_identity',
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                ),
                $storeId
            );
            $fromName = $sender['name'];
            $fromEmail = $sender['email'];
        }
        if (!$toEmail) {
            $toName = $this->scopeConfig->getValue('contact/forms/to_name', ScopeInterface::SCOPE_STORE, $storeId);
            $toEmail = $this->scopeConfig->getValue('contact/forms/to_email', ScopeInterface::SCOPE_STORE, $storeId);
        }

        try {
            /* @var $postRecord PostRecord */
            $post = $this->getRequest()->getPostValue('data');
            $postRecord = $this->postRecordFactory->create();
            $this->resourceRecord->save(
                $postRecord->addData(
                    [
                        PostRecord::FIELD_FORM_ID    => $formId,
                        PostRecord::FIELD_NAME       => $form->getDataByKey(Form::FIELD_NAME),
                        PostRecord::FIELD_DATA       => json_encode($post),
                        PostRecord::FIELD_FROM_NAME  => $fromName,
                        PostRecord::FIELD_FROM_EMAIL => $fromEmail,
                        PostRecord::FIELD_TO_NAME    => $toName,
                        PostRecord::FIELD_TO_EMAIL   => $toEmail
                    ]
                )
            );

            $templateId = $this->scopeConfig->getValue(
                'contact/forms/notification_template',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $data = [];
            foreach ($post as $key => $value) {
                $data[] = ['field' => $key, 'value' => $value];
            }
            $this->transportBuilder->setTemplateIdentifier($templateId);
            $this->transportBuilder->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId]);
            $this->transportBuilder->setTemplateVars(['data' => $data]);
            $this->transportBuilder->setFromByScope(['name' => $fromName, 'email' => $fromEmail], $storeId);
            $this->transportBuilder->addTo($toEmail, $toName);
            $this->transportBuilder->getTransport()->sendMessage();

            $this->messageManager->addSuccessMessage(__('Form post successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Failed to post the form.'));
        }

        return $resultRedirect->setUrl($redirectUrl);
    }
}
