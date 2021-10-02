<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Controller\Adminhtml\Form;

use CrazyCat\Base\Controller\Adminhtml\AbstractEditAction;
use CrazyCat\Forms\Model\Form as Model;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Edit extends AbstractEditAction
{
    public const ADMIN_RESOURCE = 'CrazyCat_Forms::forms_form';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->render(
            Model::class,
            'Specified form does not exist.',
            'CrazyCat_Forms::forms_form',
            'Create Form',
            'Edit Form (ID: %1)'
        );
    }
}
