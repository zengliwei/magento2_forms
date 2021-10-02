<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Controller\Adminhtml\Form\Widget;

use Magento\Backend\App\Action;
use Magento\Backend\Block\Template;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Chooser extends Action
{
    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @param LayoutFactory  $layoutFactory
     * @param Action\Context $context
     */
    public function __construct(
        LayoutFactory $layoutFactory,
        Action\Context $context
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Layout $resultLayout */
        /** @var Template $block */
        $resultLayout = $this->layoutFactory->create();
        $block = $resultLayout->getLayout()->getBlock('forms.form.widget.chooser');
        $block->setData('id', $this->getRequest()->getParam('uid'));
        return $resultLayout;
    }
}
