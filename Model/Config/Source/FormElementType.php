<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Model\Config\Source;

use CrazyCat\Forms\Model\Form;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class FormElementType implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Text'), 'value' => Form::ELEMENT_TYPE_TEXT],
            ['label' => __('Date'), 'value' => Form::ELEMENT_TYPE_DATE],
            ['label' => __('Text Area'), 'value' => Form::ELEMENT_TYPE_TEXTAREA],
            ['label' => __('Editor'), 'value' => Form::ELEMENT_TYPE_EDITOR],
            ['label' => __('Select'), 'value' => Form::ELEMENT_TYPE_SELECT],
            ['label' => __('Multiselect'), 'value' => Form::ELEMENT_TYPE_MULTISELECT],
            ['label' => __('Country'), 'value' => Form::ELEMENT_TYPE_COUNTRY],
            ['label' => __('Region'), 'value' => Form::ELEMENT_TYPE_REGION],
            ['label' => __('Post Code'), 'value' => Form::ELEMENT_TYPE_POSTCODE]
        ];
    }
}
