<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Conversify\ScriptManager\Tests\Model;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\TestCase;
use Conversify\ScriptManager\Model\PageData;

/**
 * @coversDefaultClass \Conversify\ScriptManager\Model\PageData
 */
class PageDataTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getPageType
     *
     * @dataProvider setPageTypeDataProvider
     */
    public function testGetPageType(
        string $fullActionName,
        string $expectedResult,
        bool $hasValidCategory,
        bool $hasValidProduct
    ): void {
        $subject = new PageData(
            $this->createRequestMock($fullActionName),
            $this->createRegistryMock($hasValidCategory, $hasValidProduct),
            $this->createStub(CheckoutSession::class),
            $this->createStub(CustomerSession::class)
        );

        $this->assertEquals($expectedResult, $subject->getPageType());
    }

    /**
     * @covers ::__construct
     * @covers ::getCartData
     * @covers ::getQuote
     *
     * @dataProvider setCartDataDataProvider
     */
    public function testGetCartData(
        bool $hasValidQuote,
        int $itemCount
    ): void {
        $subject = new PageData(
            $this->createStub(Http::class),
            $this->createStub(Registry::class),
            $this->createCheckoutSessionMock($hasValidQuote, $itemCount),
            $this->createStub(CustomerSession::class)
        );

        $result = $subject->getCartData();

        $this->assertIsArray($result);

        if ($hasValidQuote) {
            $this->assertArrayHasKey('grand_total', $result);
            $this->assertCount($itemCount, $result['contents']);
        }

        $hasValidQuote
            ?
            : $this->assertEmpty($result);
    }

    private function createRequestMock(string $fullNameAction): Http
    {
        $request = $this->createMock(Http::class);
        $request->expects(self::once())
            ->method('getFullActionName')
            ->willReturn($fullNameAction);

        return $request;
    }

    private function createRegistryMock(
        bool $hasValidCategory,
        bool $hasValidProduct
    ): Registry {
        $registry = $this->createMock(Registry::class);
        $registry->expects(self::any())
            ->method('registry')
            ->willReturn(
                $hasValidCategory
                    ? $this->createStub(CategoryInterface::class)
                    : (
                        $hasValidProduct
                            ? $this->createStub(ProductInterface::class)
                            : false
                    )
            );

        return $registry;
    }

    private function createCheckoutSessionMock(
        bool $hasValidQuote,
        int $itemCount
    ): CheckoutSession {
        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->addMethods(['getGrandTotal'])
            ->onlyMethods(['getAllItems'])
            ->getMock();

        $quote->expects(self::exactly($hasValidQuote ? 2 : 1))
            ->method('getGrandTotal')
            ->willReturn($hasValidQuote ? 50.00 : 0.00);

        $quoteItem = $this->createMock(Quote\Item::class);
        $quoteItem->expects(self::exactly($hasValidQuote ? $itemCount : 0))
            ->method('getProduct')
            ->willReturn($this->createStub(ProductInterface::class));

        $quote->expects($hasValidQuote ? self::once() : self::never())
            ->method('getAllItems')
            ->willReturn(
                array_fill(0, $itemCount, $quoteItem)
            );

        $checkoutSession = $this->createMock(CheckoutSession::class);
        $checkoutSession->expects(self::once())
            ->method('getQuote')
            ->willReturn($quote);

        return $checkoutSession;
    }

    public function setPageTypeDataProvider(): array
    {
        return [
            'homepage' => [
                'cms_index_index',
                PageData::PAGE_TYPE_HOMEPAGE,
                false,
                false
            ],
            'validCategory' => [
                'catalog_category_view',
                PageData::PAGE_TYPE_PRODUCT_LIST,
                true,
                false
            ],
            'inValidCategory' => [
                'catalog_category_view',
                PageData::PAGE_TYPE_MISCELLANEOUS,
                false,
                false
            ],
            'validProduct' => [
                'catalog_product_view',
                PageData::PAGE_TYPE_PRODUCT,
                false,
                true
            ],
            'inValidProduct' => [
                'catalog_product_view',
                PageData::PAGE_TYPE_MISCELLANEOUS,
                false,
                false
            ],
            'checkout' => [
                'checkout_onepage_index',
                PageData::PAGE_TYPE_CHECKOUT_PROCESS,
                false,
                false
            ],
            'checkoutSuccess' => [
                'checkout_onepage_success',
                PageData::PAGE_TYPE_CHECKOUT_SUCCESS,
                false,
                false
            ],
            'cart' => [
                'checkout_cart_index',
                PageData::PAGE_TYPE_CART,
                false,
                false
            ],
            'accountPage' => [
                'customer_account_index',
                PageData::PAGE_TYPE_MISCELLANEOUS,
                false,
                false
            ],
        ];
    }

    public function setCartDataDataProvider(): array
    {
        return [
            'validCart' => [true, 5],
            'invalidCart' => [false, 8]
        ];
    }
}
