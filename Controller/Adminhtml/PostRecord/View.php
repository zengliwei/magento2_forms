<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Controller\Adminhtml\PostRecord;

use CrazyCat\Base\Controller\Adminhtml\AbstractEditAction;
use CrazyCat\Forms\Model\Form\PostRecord as Model;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class View extends AbstractEditAction
{
    public const ADMIN_RESOURCE = 'CrazyCat_Forms::forms_post_record';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->render(
            Model::class,
            'Specified record does not exist.',
            'CrazyCat_Forms::forms_post_record',
            'Create Record',
            'View Record (ID: %1)'
        );
    }
}
