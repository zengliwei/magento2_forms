<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Block\Adminhtml\Form\Widget;

use CrazyCat\Forms\Model\FormFactory;
use CrazyCat\Forms\Model\ResourceModel\Form as ResourceModel;
use CrazyCat\Forms\Model\ResourceModel\Form\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Chooser extends Extended
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var ResourceModel
     */
    private $resourceModel;

    /**
     * @param CollectionFactory $collectionFactory
     * @param FormFactory       $formFactory
     * @param ResourceModel     $resourceModel
     * @param Context           $context
     * @param Data              $backendHelper
     * @param array             $data
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        FormFactory $formFactory,
        ResourceModel $resourceModel,
        Context $context,
        Data $backendHelper,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->formFactory = $formFactory;
        $this->resourceModel = $resourceModel;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritDoc
     */
    public function getGridUrl()
    {
        return $this->getUrl('forms/form_widget/chooser', ['_current' => true]);
    }

    /**
     * @inheritDoc
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        return <<<EOF
        function (grid, event) {
            const el = $chooserJsObject;
            const trElement = Event.findElement(event, 'tr');
            const blockId = trElement.down('td.col-chooser_identifier').innerHTML.replace(/^\s+|\s+$/g, '');
            const blockTitle = trElement.down('td.col-chooser_title').innerHTML.replace(/^\s+|\s+$/g, '');
            el.setElementValue(blockId);
            el.setElementLabel(blockTitle);
            el.close();
        }
EOF;
    }

    /**
     * Prepare element HTML
     *
     * @param AbstractElement $element
     * @return AbstractElement
     * @throws LocalizedException
     * @see \Magento\Widget\Block\Adminhtml\Widget\Options::_addField
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        /** @var \Magento\Widget\Block\Adminhtml\Widget\Chooser $block */
        $block = $this->getLayout()->createBlock(\Magento\Widget\Block\Adminhtml\Widget\Chooser::class);
        $uid = $this->mathRandom->getUniqueHash($element->getId());
        $block->addData(
            [
                'element' => $element,
                'uniq_id' => $uid,
                'config' => $this->getData('config'),
                'fieldset_id' => $this->getData('fieldset_id'),
                'source_url' => $this->getUrl('forms/form_widget/chooser', ['uid' => $uid])
            ]
        );
        if (($id = $element->getData('value'))) {
            $model = $this->formFactory->create();
            $this->resourceModel->load($model, $id);
            if ($model->getId()) {
                $block->setLabel($this->escapeHtml($model->getData('name')));
            }
        }
        return $element->setData('after_element_html', $block->toHtml());
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->collectionFactory->create());
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('chooser_id', ['header' => __('ID'), 'index' => 'id']);
        $this->addColumn('chooser_title', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('chooser_identifier', ['header' => __('Identifier'), 'index' => 'identifier']);
        $this->addColumn('chooser_template', ['header' => __('Template'), 'index' => 'template']);
        return parent::_prepareColumns();
    }
}
