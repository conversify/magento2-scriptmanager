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

class Script extends Template
{
    public function __construct(
        Template\Context $context,
        private Config $config,
        private PageData $dataModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getApiKey(): string
    {
        return $this->config->getApiKey();
    }

    public function getPageType(): string
    {
        return $this->dataModel->getPageType();
    }

    public function getEnableSearch(): bool
    {
        return $this->config->getEnableSearch();
    }

    public function getConversifyUiId(): string
    {
        return $this->config->getUiId();
    }

    protected function _toHtml(): string
    {
        return $this->isEnabled()
            ? parent::_toHtml()
            : '';
    }

    private function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }
}
