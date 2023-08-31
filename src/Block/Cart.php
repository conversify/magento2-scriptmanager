<?php

/**
 * Conversify
 *
 * This Magento plugin makes it easy to integrate Conversify in your webshop
 */

declare(strict_types=1);

namespace Conversify\ScriptManager\Block;

use Conversify\ScriptManager\Model\Config;
use Conversify\ScriptManager\Model\PageData;
use Magento\Framework\View\Element\Template;

class Cart extends Template
{
    public function __construct(
        Template\Context $context,
        private PageData $pageData,
        private Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getCartData(): array
    {
        return $this->pageData->getCartData();
    }

    protected function _toHtml(): string
    {
        return $this->config->isCartDataEnabled()
            ? parent::_toHtml()
            : '';
    }
}
