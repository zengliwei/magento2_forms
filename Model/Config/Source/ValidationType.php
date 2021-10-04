<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class ValidationType implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['label' => 'Required', 'value' => 'required-entry'],
            ['label' => 'Email', 'value' => 'validate-email'],
            ['label' => 'Emails', 'value' => 'validate-emails'],
            ['label' => 'URL', 'value' => 'validate-url'],
            ['label' => 'Alpha', 'value' => 'validate-alpha'],
            ['label' => 'Alpha or Number', 'value' => 'validate-alphanum'],
            ['label' => 'Date', 'value' => 'validate-date'],
            ['label' => 'Digits', 'value' => 'validate-digits'],
            ['label' => 'Number', 'value' => 'validate-number'],
            ['label' => 'Zero or greater', 'value' => 'validate-zero-or-greater']
        ];
    }
}
