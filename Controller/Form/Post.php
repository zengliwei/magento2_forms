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
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Url\DecoderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Post implements HttpPostActionInterface
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var PostRecordFactory
     */
    protected $postRecordFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResourceForm
     */
    protected $resourceForm;

    /**
     * @var ResourceRecord
     */
    protected $resourceRecord;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

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
     * @param MessageManagerInterface $messageManager
     * @param PostRecordFactory       $postRecordFactory
     * @param RequestInterface        $request
     * @param ResourceForm            $resourceForm
     * @param ResourceRecord          $resourceRecord
     * @param ResultFactory           $resultFactory
     * @param ScopeConfigInterface    $scopeConfig
     * @param SenderResolverInterface $senderResolver
     * @param StoreManagerInterface   $storeManager
     * @param TransportBuilder        $transportBuilder
     */
    public function __construct(
        DecoderInterface $urlDecoder,
        FormFactory $formFactory,
        MessageManagerInterface $messageManager,
        PostRecordFactory $postRecordFactory,
        RequestInterface $request,
        ResourceForm $resourceForm,
        ResourceRecord $resourceRecord,
        ResultFactory $resultFactory,
        ScopeConfigInterface $scopeConfig,
        SenderResolverInterface $senderResolver,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder
    ) {
        $this->formFactory = $formFactory;
        $this->messageManager = $messageManager;
        $this->postRecordFactory = $postRecordFactory;
        $this->request = $request;
        $this->resourceForm = $resourceForm;
        $this->resourceRecord = $resourceRecord;
        $this->resultFactory = $resultFactory;
        $this->scopeConfig = $scopeConfig;
        $this->senderResolver = $senderResolver;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->urlDecoder = $urlDecoder;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var $resultRedirect Redirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $redirectUrl = $this->urlDecoder->decode(
            $this->request->getParam(ActionInterface::PARAM_NAME_URL_ENCODED)
        );

        /** @var $form Form */
        $formId = $this->request->getParam('id');
        $form = $this->formFactory->create();
        $this->resourceForm->load($form, $formId);
        if (!$form->getId()) {
            return $resultRedirect->setUrl($redirectUrl);
        }

        $storeId = $this->storeManager->getStore()->getId();
        $sender = $this->senderResolver->resolve($form->getDataByKey(Form::FIELD_SENDER), $storeId);
        $recipients = preg_split('/\s*,\s*/', $form->getDataByKey(Form::FIELD_RECIPIENTS));

        try {
            $recordData = $emailData = [];
            $post = $this->request->getPostValue('data');
            if ($form->getDataByKey(Form::FIELD_RENDERER) == Form::RENDERER_CONFIG_ELEMENTS) {
                $mappings = [];
                foreach ($form->getDataByKey(Form::FIELD_ELEMENTS) as $elementSource) {
                    $mappings[$elementSource[Form::ELEMENT_IDENTIFIER]] = $elementSource[Form::ELEMENT_LABEL];
                }
                foreach ($post as $key => $value) {
                    $value = nl2br($value);
                    $recordData[$mappings[$key]] = $value;
                    $emailData[] = ['field' => $mappings[$key], 'value' => $value];
                }
            } else {
                foreach ($post as $key => $value) {
                    $value = nl2br($value);
                    $recordData[$key] = $value;
                    $emailData[] = ['field' => $key, 'value' => $value];
                }
            }

            /** @var $postRecord PostRecord */
            $postRecord = $this->postRecordFactory->create();
            $this->resourceRecord->save(
                $postRecord->addData(
                    [
                        PostRecord::FIELD_FORM_ID    => $formId,
                        PostRecord::FIELD_NAME       => $form->getDataByKey(Form::FIELD_NAME),
                        PostRecord::FIELD_DATA       => json_encode($recordData),
                        PostRecord::FIELD_FROM_NAME  => $sender['name'],
                        PostRecord::FIELD_FROM_EMAIL => $sender['email'],
                        PostRecord::FIELD_RECIPIENTS => implode(', ', $recipients)
                    ]
                )
            );

            $templateId = $this->scopeConfig->getValue(
                'contact/forms/notification_template',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $this->transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                ->setTemplateVars(['data' => $emailData])
                ->setFromByScope(['name' => $sender['name'], 'email' => $sender['email']], $storeId)
                ->addTo($recipients)
                ->getTransport()
                ->sendMessage();

            $this->messageManager->addSuccessMessage(__('Form post successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Failed to post the form.'));
        }

        return $resultRedirect->setUrl($redirectUrl);
    }
}
