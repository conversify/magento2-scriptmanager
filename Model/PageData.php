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

namespace Conversify\ScriptManager\Model;

use \Magento\Framework\Registry;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Customer\Model\Session as CustomerSession;
use \Magento\Checkout\Model\Session as CheckoutSession;
use \Magento\CatalogInventory\Api\StockRegistryInterface;

class PageData extends \Magento\Framework\DataObject
{
    const PRODUCT          = 'product';
    const INDEX            = 'index';
    const PLIST            = 'list';
    const CHECKOUT_PROCESS = 'checkout_process';
    const CHECKOUT_SUCCESS = 'checkout_success';
    const CART             = 'shoppingcart';
    const MISC             = 'misc';

    /**
     * @var \Magento\Quote\Model\Quote | null
     */
    protected $_quote = null;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession = null;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $_context = null;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession = null;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistryInterface = null;

    /**
     * @var string
     */
    protected $fullActionName = null;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        StockRegistryInterface $stockRegistryInterface,
        Registry $registry
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->_context = $context;
        $this->_coreRegistry = $registry;
        $this->_checkoutSession = $checkoutSession;
        $this->_stockRegistryInterface = $stockRegistryInterface;

        $this->fullActionName = $this->_context->getRequest()->getFullActionName();

        $this->setPageTypeData()
             ->setProductData()
             ->setCartData();
    }

    /**
     * Get pagetype
     *
     * @return string
     */
    public function getPageType() {
        try {
            switch($this->fullActionName) {
                case 'cms_index_index';
                    return self::INDEX;
                case 'catalog_category_view':
                    return $this->_coreRegistry->registry('current_category') ? self::PLIST : self::MISC;
                case 'catalog_product_view':
                    return $this->_coreRegistry->registry('current_product') ?
                        self::PRODUCT : self::MISC;
                case 'checkout_onepage_index':
                    return self::CHECKOUT_PROCESS;
                case 'checkout_onepage_success':
                    return self::CHECKOUT_SUCCESS;
            }
        } catch (\Exception $e) {}

        if (stripos($_SERVER['REQUEST_URI'], '/onestepcheckout') !== false) {
            return self::CHECKOUT_PROCESS;
        }

        if (stripos($_SERVER['REQUEST_URI'], 'checkout/cart') !== false) {
            return self::CART;
        }

        return self::MISC;
    }

    /**
     * Set page type
     *
     * @return $this
     */
    protected function setPageTypeData() {
        return $this->setData('pagetype', $this->getPageType());
    }

    /**
     * Set product data
     *
     * @return $this
     */
    protected function setProductData() {
        if($this->fullActionName === 'catalog_product_view'
           && $_product = $this->_coreRegistry->registry('current_product')
        ) {
            $_stockInfo = $this->_stockRegistryInterface
                               ->getStockItem($_product->getId());
            $product = [];
            $product['id'] = $_product->getId();
            $product['sku'] = $_product->getSku();
            $product['name'] = $_product->getName();
            $product['stock'] = $_stockInfo->getIsInStock() ? intval($_stockInfo->getQty()) : 0;
            return $this->setData('product', $product);
        }

        return $this;
    }

    /**
     * Set cart data
     *
     * @return $this
     */
    protected function setCartData() {
        $quote = $this->getQuote();
        $contents = [];

        if (is_null($quote->getGrandTotal())) {
            return $this;
        }

        foreach ($quote->getAllVisibleItems() as $item) {
            $contents[] = array(
                'name'    => $item->getName(),
                'sku'     => $item->getSku(),
                'qty'     => $item->getQty(),
                'id'      => $item->getProductId(),
                'price'   => $item->getPrice(),
                'row'     => $item->getRowTotal(),
                'row_tax' => $item->getRowTotalInclTax()
            );
        }

        return $this->setData('cart', array(
            'grand_total' => $quote->getGrandTotal(),
            'logged_in' => $this->_customerSession->isLoggedIn(),
            'contents' => $contents
        ));
    }

    /**
     * Get active quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->_checkoutSession->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Return self as array
     *
     * @param string $name
     * @param mix $value
     * @return array
     */
    public function getData($key = '', $index = null) {
        return $this->toArray();
    }
}
