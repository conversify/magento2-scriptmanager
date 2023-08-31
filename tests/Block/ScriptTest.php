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
use PHPUnit\Framework\TestCase;
use Conversify\ScriptManager\Block\Script;
use ReflectionMethod;

/**
 * @coversDefaultClass \Conversify\ScriptManager\Block\Script
 */
class ScriptTest extends TestCase
{
    private Script $subject;

    protected function setUp(): void
    {
        $this->subject = new Script(
            $this->createStub(Context::class),
            $this->createConfigMock(),
            $this->createPageDataMock(),
        );
    }

    /**
     * @covers ::__construct
     * @covers ::getApiKey
     */
    public function testGetApiKey(): void
    {
        $this->assertIsString($this->subject->getApiKey());
    }

    /**
     * @covers ::__construct
     * @covers ::getPageType
     */
    public function testGetPageType(): void
    {
        $this->assertIsString($this->subject->getPageType());
    }

    /**
     * @covers ::__construct
     * @covers ::getEnableSearch
     */
    public function testGetEnableSearch(): void
    {
        $this->assertIsBool($this->subject->getEnableSearch());
    }

    /**
     * @covers ::__construct
     * @covers ::getConversifyUiId
     */
    public function testGetConversifyUiId(): void
    {
        $this->assertIsString($this->subject->getConversifyUiId());
    }

    /**
     * @covers ::__construct
     * @covers ::_toHtml
     */
    public function testToHtml(): void
    {
        $reflectionMethod = new ReflectionMethod($this->subject, '_toHtml');

        $this->assertIsString(
            $reflectionMethod->invoke($this->subject)
        );
    }

    /**
     * @covers ::__construct
     * @covers ::isEnabled
     */
    public function testIsEnabled(): void
    {
        $reflectionMethod = new ReflectionMethod($this->subject, 'isEnabled');

        $this->assertIsBool(
            $reflectionMethod->invoke($this->subject)
        );
    }

    private function createConfigMock(): Config
    {
        $config = $this->createMock(Config::class);
        $config->expects(self::any())
            ->method('getApiKey')
            ->willReturn('foobar12345');

        $config->expects(self::any())
            ->method('isSearchEnabled')
            ->willReturn(true);

        $config->expects(self::any())
            ->method('getUiId')
            ->willReturn('foobar12345');

        $config->expects(self::any())
            ->method('isEnabled')
            ->willReturn(true);

        return $config;
    }

    private function createPageDataMock(): PageData
    {
        $pageData = $this->createMock(PageData::class);
        $pageData->expects(self::any())
            ->method('getPageType')
            ->willReturn('product');

        return $pageData;
    }
}
