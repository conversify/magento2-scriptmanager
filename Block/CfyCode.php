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

namespace Conversify\ScriptManager\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Cookie\Helper\Cookie as CookieHelper;
use Conversify\ScriptManager\Helper\Data as DataHelper;
/**
 * Conversify Tag Page Block
 */
class CfyCode extends Template {
    /**
     * Conversify data helper
     *
     * @var Conversify\ScriptManager\Helper\Data
     */
    protected $_dataHelper = null;

    /**
     * Cookie Helper
     *
     * @var \Magento\Cookie\Helper\Cookie
     */

    protected $_cookieHelper = null;
    /**
     * @param Context $context
     * @param CookieHelper $cookieHelper
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CookieHelper $cookieHelper,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->_cookieHelper = $cookieHelper;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get API Key
     *
     * @return string
     */
    public function getApiKey() {
        return $this->_dataHelper->getApiKey();
    }

    /**
     * Render JS
     *
     * @return string
     */
    protected function _toHtml() {
       if (!$this->_dataHelper->isEnabled()) {
            return '<!-- Conversify not enabled -->';
        }
        return parent::_toHtml();
    }
}
