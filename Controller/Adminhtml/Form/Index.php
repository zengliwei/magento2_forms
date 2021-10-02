<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Controller\Adminhtml\Form;

use CrazyCat\Base\Controller\Adminhtml\AbstractIndexAction;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Index extends AbstractIndexAction
{
    public const ADMIN_RESOURCE = 'CrazyCat_Forms::form';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->render(
            'forms_form',
            'CrazyCat_Forms::forms_form',
            'Custom Forms'
        );
    }
}
