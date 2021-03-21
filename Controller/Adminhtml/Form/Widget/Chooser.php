<?php
/*
 * Copyright (c) 2020 Zengliwei
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Common\Forms\Controller\Adminhtml\Form\Widget;

use Magento\Backend\App\Action;
use Magento\Backend\Block\Template;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;

/**
 * @package Common\Forms
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Chooser extends Action
{
    /**
     * @var LayoutFactory
     */
    protected LayoutFactory $layoutFactory;

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
        /* @var Layout $resultLayout */
        /* @var Template $block */
        $resultLayout = $this->layoutFactory->create();
        $block = $resultLayout->getLayout()->getBlock('forms.form.widget.chooser');
        $block->setData('id', $this->getRequest()->getParam('uid'));
        return $resultLayout;
    }
}
