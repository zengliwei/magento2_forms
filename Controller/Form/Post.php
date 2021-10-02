<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Controller\Form;

use CrazyCat\Forms\Model\Form;
use CrazyCat\Forms\Model\Form\PostRecord;
use CrazyCat\Forms\Model\Form\PostRecordFactory;
use CrazyCat\Forms\Model\FormFactory;
use CrazyCat\Forms\Model\ResourceModel\Form as ResourceForm;
use CrazyCat\Forms\Model\ResourceModel\Form\PostRecord as ResourceRecord;
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
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Post extends Action implements HttpPostActionInterface
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var PostRecordFactory
     */
    protected $postRecordFactory;

    /**
     * @var ResourceForm
     */
    private $resourceForm;

    /**
     * @var ResourceRecord
     */
    protected $resourceRecord;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

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
        /** @var $resultRedirect Redirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $redirectUrl = $this->urlDecoder->decode(
            $this->getRequest()->getParam(ActionInterface::PARAM_NAME_URL_ENCODED)
        );

        /** @var $form Form */
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
            /** @var $postRecord PostRecord */
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
