<?php

/**
 * Conversify
 *
 * This Magento plugin makes it easy to integrate Conversify in your webshop
 */

declare(strict_types=1);

namespace Conversify\ScriptManager\Model;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    private const XML_PATH_ACTIVE = 'scriptmanager/general/active',
        XML_PATH_APIKEY           = 'scriptmanager/general/apikey',
        XML_PATH_ENABLE_SEARCH    = 'scriptmanager/search/enable',
        XML_PATH_UI_ID            = 'scriptmanager/search/ui_id';

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

    public function getEnableSearch(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_SEARCH,
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
}
