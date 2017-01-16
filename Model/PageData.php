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
use \Magento\CatalogInventory\Model\Stock\StockItemRepository;
use \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;

class PageData extends \Magento\Framework\DataObject
{
    const PRODUCT          = 'product';
    const INDEX            = 'index';
    const LIST             = 'list';
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
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $_stockItemRepository = null;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $_itemOrderFactory = null;

    /**
     * @var string
     */
    protected $fullActionName = null;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        StockItemRepository $stockItemRepository,
        CollectionFactory $itemOrderFactory,
        Registry $registry
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->_context = $context;
        $this->_coreRegistry = $registry;
        $this->_checkoutSession = $checkoutSession;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_itemOrderFactory = $itemOrderFactory;

        $this->fullActionName = $this->_context->getRequest()->getFullActionName();

        $this->setPageTypeData()
             ->setProductData()
             ->setCartData()
             ->setLastOrderData();
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
                    return $this->_coreRegistry->registry('current_category') ? self::LIST : self::MISC;
                case 'catalog_product_view':
                    return $this->_coreRegistry->registry('current_product') ?
                        self::PRODUCT : self::MISC;
                case 'checkout_onepage_index':
                    return self::CHECKOUT_PROCESS;
                case 'checkout_onepage_success':
                    return self::CHECKOUT_SUCCESS;
            }
        } catch (Exception $e) {}

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
            $_stockInfo = $this->_stockItemRepository->get($_product->getId());
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
     * Set last ordered data
     *
     * @return $this
     */
    protected function setLastOrderData() {
        $_product = $this->_coreRegistry->registry('product');
        if ($this->getPageType() === self::PRODUCT
            && $_productId = $_product->getId()) {
            $_productLastOrder = $this->getLastOrder(array(
                'product_id' => $_productId
            ));

            if ($_productLastOrder) {
                $this->setData('product_last_order', $_productLastOrder);
            }
        }

        $_lastOrder = $this->getLastOrder();
        if ($_lastOrder) {
            $this->setData('last_order', $_lastOrder);
        }

        return $this;
    }

    /**
     * Get an order items collection
     *
     * @param array $filters
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected function getOrderCollection(array $filters=[]) {
        $_itemCollection = $this->_itemOrderFactory->create();
        $_itemCollection->addAttributeToSort('created_at', 'desc');

        foreach ($filters as $field => $condition) {
            $_itemCollection->addFieldToFilter($field, $condition);
        }

        return $_itemCollection;
    }

    /**
     * Get last order
     *
     * @param array $filters
     * @return \Magento\Sales\Model\Order | void
     */
    protected function getLastOrder(array $filters=[]) {
        $_order = $this->getOrderCollection($filters)->getFirstItem();
        if ($_order) {
            return strtotime($_order->getCreatedAt());
        }
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
