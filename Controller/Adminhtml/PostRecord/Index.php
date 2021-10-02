<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Controller\Adminhtml\PostRecord;

use CrazyCat\Base\Controller\Adminhtml\AbstractIndexAction;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Index extends AbstractIndexAction
{
    public const ADMIN_RESOURCE = 'CrazyCat_Forms::forms_post_record';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->render(
            'forms_record',
            'CrazyCat_Forms::forms_post_record',
            'Post Record'
        );
    }
}
