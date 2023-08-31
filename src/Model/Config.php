<?php

/**
 * Conversify
 *
 * This Magento plugin makes it easy to integrate Conversify in your webshop
 */

declare(strict_types=1);

namespace Conversify\ScriptManager\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {
    }

    private const XML_PATH_ACTIVE = 'scriptmanager/general/active',
        XML_PATH_APIKEY           = 'scriptmanager/general/apikey',
        XML_PATH_SEARCH_ENABLED   = 'scriptmanager/search/enable',
        XML_PATH_UI_ID            = 'scriptmanager/search/ui_id',
        XML_PATH_CART_ENABLED     = 'scriptmanager/cart/enable';

    public function isEnabled(): bool
    {
        return $this->getApiKey() &&
            $this->scopeConfig->isSetFlag(
                self::XML_PATH_ACTIVE,
                ScopeInterface::SCOPE_STORE
            );
    }

    public function getApiKey(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_APIKEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isSearchEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SEARCH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getUiId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_UI_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isCartDataEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CART_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
