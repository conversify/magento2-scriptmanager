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

declare(strict_types=1);

namespace Conversify\ScriptManager\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;

class PageData
{
    public const PAGE_TYPE_PRODUCT = 'product',
        PAGE_TYPE_HOMEPAGE          = 'index',
        PAGE_TYPE_PRODUCT_LIST      = 'list',
        PAGE_TYPE_CHECKOUT_PROCESS  = 'checkout_process',
        PAGE_TYPE_CHECKOUT_SUCCESS  = 'checkout_success',
        PAGE_TYPE_CART              = 'shoppingcart',
        PAGE_TYPE_MISCELLANEOUS     = 'misc';

    public function __construct(
        private Http $request,
        private Registry $registry,
        private CheckoutSession $checkoutSession,
        private CustomerSession $customerSession
    ) {
    }

    public function getPageType(): string
    {
        return match ($this->request->getFullActionName()) {
            'cms_index_index' => self::PAGE_TYPE_HOMEPAGE,
            'catalog_category_view' => $this->registry->registry('current_category')
                ? self::PAGE_TYPE_PRODUCT_LIST
                : self::PAGE_TYPE_MISCELLANEOUS,
            'catalog_product_view' => $this->registry->registry('current_product')
                ? self::PAGE_TYPE_PRODUCT
                : self::PAGE_TYPE_MISCELLANEOUS,
            'checkout_onepage_index' => self::PAGE_TYPE_CHECKOUT_PROCESS,
            'checkout_cart_index' => self::PAGE_TYPE_CART,
            'checkout_onepage_success' => self::PAGE_TYPE_CHECKOUT_SUCCESS,
            default => self::PAGE_TYPE_MISCELLANEOUS
        };
    }

    public function getCartData(): array
    {
        $quote = $this->getQuote();

        if (!$quote->getGrandTotal()) {
            return [];
        }

        return [
            'grand_total' => $quote->getGrandTotal(),
            'logged_in' => $this->customerSession->isLoggedIn(),
            'contents' => array_map(
                static fn (Quote\Item $item) => [
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'qty' => $item->getQty(),
                    'id' => $item->getProduct()->getId(),
                    'price' => $item->getPrice(),
                    'row' => $item->getRowTotal(),
                    'row_tax' => $item->getRowTotalInclTax()
                ],
                $quote->getAllItems()
            )
        ];
    }

    private function getQuote(): Quote
    {
        return $this->checkoutSession->getQuote();
    }
}
