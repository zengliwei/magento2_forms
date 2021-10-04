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
    public const FIELD_SENDER = 'sender';
    public const FIELD_RECIPIENTS = 'recipients';
    public const FIELD_RENDERER = 'renderer';
    public const FIELD_TEMPLATE = 'template';
    public const FIELD_ELEMENTS = 'elements';

    public const ELEMENT_LABEL = 'label';
    public const ELEMENT_IDENTIFIER = 'identifier';
    public const ELEMENT_TYPE = 'type';
    public const ELEMENT_VALIDATION = 'validation';
    public const ELEMENT_CONFIG = 'config';

    public const ELEMENT_TYPE_TEXT = 'text';
    public const ELEMENT_TYPE_DATE = 'date';
    public const ELEMENT_TYPE_TEXTAREA = 'textarea';
    public const ELEMENT_TYPE_EDITOR = 'wysiwyg';
    public const ELEMENT_TYPE_SELECT = 'select';
    public const ELEMENT_TYPE_MULTISELECT = 'multiselect';
    public const ELEMENT_TYPE_COUNTRY = 'country';
    public const ELEMENT_TYPE_REGION = 'region';
    public const ELEMENT_TYPE_POSTCODE = 'postcode';

    public const RENDERER_TEMPLATE = 'template';
    public const RENDERER_CONFIG_ELEMENTS = 'config_elements';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Form::class);
    }

    /**
     * @inheritDoc
     */
    public function afterLoad()
    {
        parent::afterLoad();
        $this->setData(self::FIELD_ELEMENTS, json_decode($this->getDataByKey(self::FIELD_ELEMENTS), true));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        $this->setData(self::FIELD_ELEMENTS, json_encode($this->getDataByKey(self::FIELD_ELEMENTS)));
        return parent::beforeSave();
    }
}
