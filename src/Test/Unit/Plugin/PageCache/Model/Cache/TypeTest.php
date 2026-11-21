<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Test\Unit\Plugin\PageCache\Model\Cache;

use Infrangible\CacheUsage\Helper\Data;
use Infrangible\CacheUsage\Model\Cache;
use Infrangible\CacheUsage\Plugin\Framework\App\PageCache\Kernel;
use Infrangible\CacheUsage\Plugin\PageCache\Model\Cache\Type;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests that the data of the request is stored and loaded with the identifier, tags and lifetime of the page.
 */
class TypeTest extends TestCase
{
    /**
     * Tests that the data is stored next to the page that the kernel stores.
     *
     * @return void
     */
    public function testAfterSaveStoresData(): void
    {
        $cache = new Cache();

        $cache->setRouteName('cms');
        $cache->setPageCacheOperation(Kernel::OPERATION_PROCESS);

        $type = $this->createMock(\Magento\PageCache\Model\Cache\Type::class);
        $type->expects($this->once())->method('save')->with(
            $this->callback(static fn(string $data): bool => str_contains($data, '"route":"cms"')),
            'page_id' . Type::IDENTIFIER_SUFFIX,
            ['cms_p_1'],
            '86400'
        );

        $this->assertTrue($this->createPlugin($cache)->afterSave($type, true, 'page', 'page_id', ['cms_p_1'], '86400'));
        $this->assertNull($cache->getPageCacheOperation());
    }

    /**
     * Tests that other entries of the cache type and failed saves store nothing.
     *
     * @return void
     */
    public function testAfterSaveOutsideOfKernel(): void
    {
        $cache = new Cache();

        $type = $this->createMock(\Magento\PageCache\Model\Cache\Type::class);
        $type->expects($this->never())->method('save');

        $plugin = $this->createPlugin($cache);

        $plugin->afterSave($type, true, 'data', 'other_id');

        $cache->setPageCacheOperation(Kernel::OPERATION_PROCESS);

        $plugin->afterSave($type, false, 'page', 'page_id');
        $plugin->afterSave($type, true, 'data', 'page_id' . Type::IDENTIFIER_SUFFIX);
    }

    /**
     * Tests that the data is loaded with the page that the kernel loads.
     *
     * @return void
     */
    public function testAfterLoadRestoresData(): void
    {
        $cache = new Cache();

        $cache->setPageCacheOperation(Kernel::OPERATION_LOAD);

        $type = $this->createMock(\Magento\PageCache\Model\Cache\Type::class);
        $type->expects($this->once())->method('load')->with('page_id' . Type::IDENTIFIER_SUFFIX)->willReturn(
            '{"route":"catalog","controller":"category","action":"view","blocks":[]}'
        );

        $this->assertSame('page', $this->createPlugin($cache)->afterLoad($type, 'page', 'page_id'));
        $this->assertSame('catalog', $cache->getRouteName());
        $this->assertSame('category', $cache->getControllerName());
    }

    /**
     * Tests that nothing is loaded for a page that is not cached, and that invalid data is only logged.
     *
     * @return void
     */
    public function testAfterLoadWithoutPageOrInvalidData(): void
    {
        $cache = new Cache();

        $cache->setPageCacheOperation(Kernel::OPERATION_LOAD);

        $type = $this->createMock(\Magento\PageCache\Model\Cache\Type::class);
        $type->expects($this->once())->method('load')->willReturn('{invalid');

        $logging = $this->createMock(LoggerInterface::class);
        $logging->expects($this->once())->method('warning');

        $plugin = new Type($this->createHelper(), $cache, new Json(), $logging);

        $this->assertFalse($plugin->afterLoad($type, false, 'page_id'));
        $this->assertSame('page', $plugin->afterLoad($type, 'page', 'page_id'));
        $this->assertNull($cache->getRouteName());
    }

    /**
     * Returns the plugin with collection enabled.
     *
     * @param Cache $cache
     *
     * @return Type
     */
    private function createPlugin(Cache $cache): Type
    {
        return new Type($this->createHelper(), $cache, new Json(), $this->createStub(LoggerInterface::class));
    }

    /**
     * Returns the configuration with collection enabled.
     *
     * @return Data
     */
    private function createHelper(): Data
    {
        $helper = $this->createStub(Data::class);
        $helper->method('isCollecting')->willReturn(true);

        return $helper;
    }
}
