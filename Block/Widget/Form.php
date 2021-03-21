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

namespace Common\Forms\Block\Widget;

use Common\Forms\Model\Form as Model;
use Common\Forms\Model\FormFactory;
use Common\Forms\Model\ResourceModel\Form as ResourceModel;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * @package Common\Forms
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Form extends Template implements BlockInterface
{
    /**
     * @var Model|null
     */
    private ?Model $form = null;

    /**
     * @var FormFactory
     */
    private FormFactory $formFactory;

    /**
     * @var ResourceModel
     */
    private ResourceModel $resourceModel;

    /**
     * @var EncoderInterface
     */
    private EncoderInterface $urlEncoder;

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
     * @return Model
     */
    public function getForm()
    {
        if ($this->form === null) {
            /* @var $form Model */
            $this->form = $this->formFactory->create();
            $this->resourceModel->load($this->form, $this->getDataByKey('identifier'), Model::FIELD_IDENTIFIER);
        }
        return $this->form;
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('forms/form/post', ['id' => $this->getForm()->getId()]);
    }

    /**
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
