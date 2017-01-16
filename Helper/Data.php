<?php
/**
 * Conversify
 *
 * This Magento plugin makes it easy to integrate Conversify in your webshop
 *
 * @category   Conversify
 * @package    Magento Plugin
 * @author     Conversfiy.com
 * @copyright  2017 Conversify.com
 */

namespace Conversify\ScriptManager\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ACTIVE       = 'scriptmanager/general/active';
    const XML_PATH_APIKEY       = 'scriptmanager/general/apikey';
    const XML_PATH_STOCK_INFO   = 'scriptmanager/general/stock_info';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * Is Conversify is enabled
     *
     * @return bool
     */
    public function isEnabled() {

        return $this->getApiKey() && $this->scopeConfig->isSetFlag(self::XML_PATH_ACTIVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Conversify API Key
     *
     * @return bool | null | string
     */
    public function getApiKey() {
        return $this->scopeConfig->getValue(self::XML_PATH_APIKEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}