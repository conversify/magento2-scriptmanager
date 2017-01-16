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

namespace Conversify\ScriptManager\Observer\Frontend;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Conversify\ScriptManager\Helper\Data as DataHelper;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

class OrderSuccessPageViewObserver implements ObserverInterface
{
    /**
     * @param LayoutInterface $layout
     * @param DataHelper $dataHelper
     * @param OrderCollection $orderCollection
     */
    public function __construct(
        LayoutInterface $layout,
        DataHelper $dataHelper,
        OrderCollection $orderCollection
    ) {
        $this->_layout = $layout;
        $this->_dataHelper = $dataHelper;
        $this->_orderCollection = $orderCollection;
    }

    /**
     * Add conversion value and order information into
     * data block to render on checkout success pages.
     *
     * @param EventObserver $observer
     * @return \Conversify\ScriptManager\Observer\Frontend\OrderSuccessPageViewObserver
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->_dataHelper->isEnabled()) {
            return $this;
        }

        $orderIds = $observer->getEvent()->getOrderIds();
        if (!$orderIds || !is_array($orderIds)) {
            return $this;
        }

        // order contents
        $contents = [];
        // order value
        $conversionValue = 0;

        $this->_orderCollection
             ->addFieldToFilter('entity_id', ['in' => $orderIds]);

        foreach ($this->_orderCollection as $order) {
            $conversionValue += $order->getBaseGrandTotal();

            // quote will have been cleared from session
            // at this point, re-create from order items.
            foreach ($order->getItemsCollection() as $item) {
                $contents[] = array(
                    'name'  => $item->getName(),
                    'sku'   => $item->getSku(),
                    'id'    => $item->getProductId(),
                    'price' => $item->getPrice(),
                    // getQtyOrdered() returns a string (...)
                    'qty'   => intval($item->getQtyOrdered())
                );
            }
        }

        $block = $this->_layout->getBlock('cfy_data');
        if ($block) {
            $this->setModelData(
                'conversion', intval(round($conversionValue * 100, 0)));

            if ($contents) {
                $block->setModelData('cart', array(
                    'grand_total' => $conversionValue,
                    'contents' => $contents)
                );
            }
        }

        return $this;
    }
}