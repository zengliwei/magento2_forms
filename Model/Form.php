<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Form extends AbstractModel
{
    public const FIELD_NAME = 'name';
    public const FIELD_IDENTIFIER = 'identifier';
    public const FIELD_TEMPLATE = 'template';
    public const FIELD_FROM_NAME = 'from_name';
    public const FIELD_FROM_EMAIL = 'from_email';
    public const FIELD_TO_NAME = 'to_name';
    public const FIELD_TO_EMAIL = 'to_email';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Form::class);
    }
}
