<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Model\Form\PostRecord;

use CrazyCat\Base\Model\AbstractDataProvider;
use CrazyCat\Forms\Model\ResourceModel\Form\PostRecord\Collection;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var string
     */
    protected $persistKey = 'forms_post_record';

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $this->initCollection(Collection::class);
    }
}
