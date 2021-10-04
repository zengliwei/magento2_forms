<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Model\ResourceModel\Form;

use CrazyCat\Forms\Model\Form as Model;
use CrazyCat\Forms\Model\ResourceModel\Form as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'forms_form_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'collection';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            $item->setData(Model::FIELD_ELEMENTS, json_decode($item->getDataByKey(Model::FIELD_ELEMENTS), true));
        }
        return $this;
    }
}
