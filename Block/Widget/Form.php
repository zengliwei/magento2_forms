<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Block\Widget;

use CrazyCat\Forms\Model\Form as Model;
use CrazyCat\Forms\Model\FormFactory;
use CrazyCat\Forms\Model\ResourceModel\Form as ResourceModel;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Form extends Template implements BlockInterface
{
    /**
     * @var Model|null
     */
    private $form = null;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var ResourceModel
     */
    private $resourceModel;

    /**
     * @var EncoderInterface
     */
    private $urlEncoder;

    /**
     * @param FormFactory      $formFactory
     * @param ResourceModel    $resourceModel
     * @param EncoderInterface $urlEncoder
     * @param Template\Context $context
     * @param array            $data
     */
    public function __construct(
        FormFactory $formFactory,
        ResourceModel $resourceModel,
        EncoderInterface $urlEncoder,
        Template\Context $context,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->resourceModel = $resourceModel;
        $this->urlEncoder = $urlEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Get form
     *
     * @return Model
     */
    public function getForm()
    {
        if ($this->form === null) {
            /** @var $form Model */
            $this->form = $this->formFactory->create();
            $this->resourceModel->load($this->form, $this->getDataByKey('identifier'), Model::FIELD_IDENTIFIER);
        }
        return $this->form;
    }

    /**
     * Get form action
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('forms/form/post', ['id' => $this->getForm()->getId()]);
    }

    /**
     * Get hidden input HTML
     *
     * @return string
     */
    public function getHiddenInputHtml()
    {
        return '<input type="hidden" name="' . ActionInterface::PARAM_NAME_URL_ENCODED . '" ' .
            'value="' . $this->urlEncoder->encode($this->_urlBuilder->getCurrentUrl()) . '"/>';
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $this->_template = $this->getForm()->getDataByKey(Model::FIELD_TEMPLATE);
        return parent::_toHtml();
    }
}
