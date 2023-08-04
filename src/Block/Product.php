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
use Magento\Catalog\Model\Product as ProductModel;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Checkout\Model\Session;
use Magento\Cookie\Helper\Cookie as CookieHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Product extends Template
{
    public function __construct(
        Template\Context $context,
        private Registry $registry,
        private StockRegistryInterface $stockRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getProductData(): array
    {
        $currentProduct = $this->registry->registry('current_product');

        if (!$currentProduct instanceof ProductModel) {
            return [];
        }

        return [
            'id' => $currentProduct->getId(),
            'sku' => $currentProduct->getSku(),
            'name' => $currentProduct->getName(),
            'stock' => $this->getProductStock($currentProduct)
        ];
    }

    private function getProductStock(ProductModel $product): float
    {
        $stockInfo = $this->stockRegistry->getStockItem($product->getId());

        return $stockInfo->getQty();
    }
}
