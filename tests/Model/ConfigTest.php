<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Conversify\ScriptManager\Tests\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Conversify\ScriptManager\Model\Config;

/**
 * @coversDefaultClass \Conversify\ScriptManager\Model\Config
 */
class ConfigTest extends TestCase
{
    private Config $subject;

    protected function setUp(): void
    {
        $this->subject = new Config(
            $this->createScopeConfigMock()
        );
    }

    /**
     * @covers ::__construct
     * @covers ::isEnabled
     */
    public function testIsEnabled(): void
    {
        $this->assertIsBool($this->subject->isEnabled());
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
     * @covers ::getUiId
     */
    public function testGetUiId(): void
    {
        $this->assertIsString($this->subject->getUiId());
    }

    /**
     * @covers ::__construct
     * @covers ::isSearchEnabled
     */
    public function testIsSearchEnabled(): void
    {
        $this->assertIsBool($this->subject->isSearchEnabled());
    }

    /**
     * @covers ::__construct
     * @covers ::isCartDataEnabled
     */
    public function testIsCartDataEnabled(): void
    {
        $this->assertIsBool($this->subject->isCartDataEnabled());
    }

    private function createScopeConfigMock(): ScopeConfigInterface
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects(self::any())
            ->method('getValue')
            ->willReturn('string');

        $scopeConfig->expects(self::any())
            ->method('isSetFlag')
            ->willReturn(true);

        return $scopeConfig;
    }
}
