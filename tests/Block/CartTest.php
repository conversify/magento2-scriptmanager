<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Conversify\ScriptManager\Tests\Block;

use Conversify\ScriptManager\Model\Config;
use Conversify\ScriptManager\Model\PageData;
use Magento\Framework\View\Element\Template\Context;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Conversify\ScriptManager\Block\Cart;
use ReflectionMethod;

/**
 * @coversDefaultClass \Conversify\ScriptManager\Block\Cart
 */
class CartTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getCartData
     */
    public function testGetCartData(): void
    {
        $subject = new Cart(
            $this->createStub(Context::class),
            $this->createPageDataMock(),
            $this->createStub(Config::class),
        );

        $this->assertIsArray($subject->getCartData());
    }

    /**
     * @covers ::__construct
     * @covers ::_toHtml
     *
     * @dataProvider setToHtmlDataProvider
     */
    public function testToHtml(
        bool $isEnabled
    ): void {
        $subject = new Cart(
            $this->createStub(Context::class),
            $this->createStub(PageData::class),
            $this->createConfigMock($isEnabled),
        );

        $reflectionMethod = new ReflectionMethod($subject, '_toHtml');
        $result = $reflectionMethod->invoke($subject);

        $this->assertIsString($result);
    }

    private function createPageDataMock(): PageData
    {
        $pageData = $this->createMock(PageData::class);
        $pageData->expects(self::once())
            ->method('getCartData')
            ->willReturn([]);

        return $pageData;
    }

    private function createConfigMock(
        bool $isEnabled
    ): Config {
        $config = $this->createMock(Config::class);
        $config->expects(self::once())
            ->method('isCartDataEnabled')
            ->willReturn($isEnabled);

        return $config;
    }

    public function setToHtmlDataProvider(): array
    {
        return [
            'enabled' => [true],
            'disabled' => [false]
        ];
    }
}
