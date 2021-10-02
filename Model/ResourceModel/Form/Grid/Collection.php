<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Model\ResourceModel\Form\Grid;

use CrazyCat\Base\Model\ResourceModel\Grid\AbstractCollection;
use CrazyCat\Forms\Model\ResourceModel\Form as ResourceModel;
use CrazyCat\Forms\Model\ResourceModel\Form\Collection as ModelCollection;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Collection extends ModelCollection implements SearchResultInterface
{
    use AbstractCollection;

    /**
     * @var string
     */
    protected $_eventPrefix = 'forms_form_grid_collection';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(Document::class, ResourceModel::class);
    }
}
