<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Conversify\ScriptManager\Tests\Block;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use PHPUnit\Framework\TestCase;
use Conversify\ScriptManager\Block\Product;

/**
 * @coversDefaultClass \Conversify\ScriptManager\Block\Product
 */
class ProductTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getProductData
     * @covers ::getProductStock
     *
     * @dataProvider setDataProvider
     */
    public function testGetProductData(
        bool $hasValidProduct
    ): void {
        $subject = new Product(
            $this->createStub(Context::class),
            $this->createRegistryMock($hasValidProduct),
            $this->createStockRegistryMock($hasValidProduct)
        );

        $result = $subject->getProductData();

        $this->assertIsArray($result);

        if ($hasValidProduct) {
            $this->assertArrayHasKey('id', $result);
        }
    }

    private function createRegistryMock(
        bool $hasValidProduct
    ): Registry {
        $registry = $this->createMock(Registry::class);
        $registry->expects(self::once())
            ->method('registry')
            ->with('current_product')
            ->willReturn(
                $hasValidProduct
                    ? $this->createMock(ProductModel::class)
                    : false
            );

        return $registry;
    }

    public function setDataProvider(): array
    {
        return [
            'hasValidProduct' => [true],
            'noValidProduct' => [false]
        ];
    }

    private function createStockRegistryMock(
        bool $hasValidProduct
    ): StockRegistryInterface {
        $expectation = $hasValidProduct
            ? self::any()
            : self::never();

        $stockItem = $this->createMock(StockItemInterface::class);
        $stockItem->expects($expectation)
            ->method('getQty')
            ->willReturn(10.000);

        $stockRegistry = $this->createMock(StockRegistryInterface::class);
        $stockRegistry->expects($expectation)
            ->method('getStockItem')
            ->willReturn($stockItem);

        return $stockRegistry;
    }
}
