<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Observer;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http as Response;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Url\DecoderInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\RequestHandlerInterface;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class ReCaptchaCheck implements ObserverInterface
{
    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var DecoderInterface
     */
    private $urlDecoder;

    /**
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param RequestHandlerInterface   $requestHandler
     * @param RequestInterface          $request
     * @param Response                  $response
     * @param DecoderInterface          $urlDecoder
     */
    public function __construct(
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        RequestHandlerInterface $requestHandler,
        RequestInterface $request,
        Response $response,
        DecoderInterface $urlDecoder
    ) {
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->request = $request;
        $this->requestHandler = $requestHandler;
        $this->response = $response;
        $this->urlDecoder = $urlDecoder;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $key = 'forms';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)) {
            $redirectUrl = $this->urlDecoder->decode(
                $this->request->getParam(ActionInterface::PARAM_NAME_URL_ENCODED)
            );
            $this->requestHandler->execute($key, $this->request, $this->response, $redirectUrl);
        }
    }
}
