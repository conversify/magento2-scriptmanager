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

use Conversify\ScriptManager\Model\PageData;
use Magento\Framework\View\Element\Template;
use Magento\Cookie\Helper\Cookie as CookieHelper;
use Magento\Framework\View\Element\Template\Context;
use Conversify\ScriptManager\Helper\Data as DataHelper;

class CfyData extends Template
{
    /**
     * @var \Conversify\ScriptManager\Helper\Data
     */
    protected $_dataHelper = null;

    /**
     * @var \Magento\Cookie\Helper\Cookie
     */

    protected $_cookieHelper = null;

    /**
     * @var \Conversify\ScriptManager\Model\PageData
     */
    protected $_dataModel = null;

    /**
     * @param Context $context
     * @param CookieHelper $cookieHelper
     * @param GtmHelper $gtmHelper
     * @param PageData $pageData
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        CookieHelper $cookieHelper,
        PageData $dataModel,
        array $data = []
    ) {
        $this->_cookieHelper = $cookieHelper;
        $this->_dataHelper = $dataHelper;
        $this->_dataModel = $dataModel;
        // to avoid cache issues
		$this->_isScopePrivate = true;
        parent::__construct($context, $data);
    }

    /**
     * Add a variable to the page data
     *
     * @return $this
     */
    public function setModelData($name, $value) {
        $this->_dataModel->setData($name, $value);
        return $this;
    }

    /**
     * Return page model data
     *
     * @return array
     */
    public function getModelData() {
        return $this->_dataModel->getData();
    }

    /**
     * Render JS data
     *
     * @return string
     */
    protected function _toHtml() {
        if ($this->_cookieHelper->isUserNotAllowSaveCookie() || !$this->_dataHelper->isEnabled()) {
            return '<!-- Conversify not enabled -->';
        }
        return parent::_toHtml();
    }
}