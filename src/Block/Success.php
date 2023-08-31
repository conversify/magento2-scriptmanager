<?php

/**
 * Conversify
 *
 * This Magento plugin makes it easy to integrate Conversify in your webshop
 */

declare(strict_types=1);

namespace Conversify\ScriptManager\Block;

use Magento\Checkout\Model\Session;
use Magento\Cookie\Helper\Cookie as CookieHelper;
use Magento\Framework\View\Element\Template;

class Success extends Template
{
    public function __construct(
        Template\Context $context,
        private Session $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getConversionValue(): int
    {
        $order = $this->checkoutSession->getLastRealOrder();

        return (int) ($order->getBaseGrandTotal() * 100);
    }
}
