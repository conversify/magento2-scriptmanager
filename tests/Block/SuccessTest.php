<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Conversify\ScriptManager\Tests\Block;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\Data\OrderInterface;
use PHPUnit\Framework\TestCase;
use Conversify\ScriptManager\Block\Success;

/**
 * @coversDefaultClass \Conversify\ScriptManager\Block\Success
 */
class SuccessTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getConversionValue
     *
     * @dataProvider setDataProvider
     */
    public function testGetConversionValue(
        int|float $conversionValue
    ): void {
        $subject = new Success(
            $this->createStub(Context::class),
            $this->createCheckoutSessionMock($conversionValue / 100)
        );

        $result = $subject->getConversionValue();
        $this->assertIsInt($result);
        $this->assertEquals($conversionValue, $result);
    }

    private function createCheckoutSessionMock(
        int|float $conversionValue
    ): Session {
        $order = $this->createMock(OrderInterface::class);
        $order->expects(self::once())
            ->method('getBaseGrandTotal')
            ->willReturn($conversionValue);

        $session = $this->createMock(Session::class);
        $session->expects(self::once())
            ->method('getLastRealOrder')
            ->willReturn($order);

        return $session;
    }

    public function setDataProvider(): array
    {
        return [
            [5000],
            [1275850]
        ];
    }
}
