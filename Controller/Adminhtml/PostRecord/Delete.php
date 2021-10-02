<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Controller\Adminhtml\PostRecord;

use CrazyCat\Base\Controller\Adminhtml\AbstractDeleteAction;
use CrazyCat\Forms\Model\Form\PostRecord as Model;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Delete extends AbstractDeleteAction
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->delete(
            Model::class,
            'Specified record does not exist.',
            'Record deleted.'
        );
    }
}
