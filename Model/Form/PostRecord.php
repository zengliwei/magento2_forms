<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Model\Form;

use CrazyCat\Forms\Model\ResourceModel\Form\PostRecord as ResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class PostRecord extends AbstractModel
{
    public const FIELD_FORM_ID = 'form_id';
    public const FIELD_NAME = 'name';
    public const FIELD_DATA = 'data';
    public const FIELD_FROM_NAME = 'from_name';
    public const FIELD_FROM_EMAIL = 'from_email';
    public const FIELD_RECIPIENTS = 'recipients';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
