<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Test\Unit\Plugin\Framework\Cache;

use Closure;
use Infrangible\CacheUsage\Helper\Data;
use Infrangible\CacheUsage\Model\Cache;
use Infrangible\CacheUsage\Plugin\Framework\Cache\LockGuardedCacheLoader;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests that loading the block cache is recorded without loading it a second time.
 */
class LockGuardedCacheLoaderTest extends TestCase
{
    /**
     * Tests that the wrapped loader records a cached block and returns the cached HTML.
     *
     * @return void
     */
    public function testCachedBlock(): void
    {
        $cache = new Cache();

        $loader = $this->wrapLoader($cache, $this->createBlockLoader('<p>cached</p>'));

        $this->assertSame('<p>cached</p>', $loader());
        $this->assertTrue($cache->getBlock('header')?->isCached());
        $this->assertFalse($cache->getBlock('header')->isUncached());
    }

    /**
     * Tests that a block that is not in the cache is recorded as uncached.
     *
     * @return void
     */
    public function testUncachedBlock(): void
    {
        $cache = new Cache();

        $loader = $this->wrapLoader($cache, $this->createBlockLoader(false));

        $this->assertFalse($loader());
        $this->assertTrue($cache->getBlock('header')?->isUncached());
    }

    /**
     * Tests that other cache entries and loaders of other objects are not changed.
     *
     * @return void
     */
    public function testOtherLoaders(): void
    {
        $plugin = new LockGuardedCacheLoader($this->createHelper(), new Cache());
        $subject = $this->createStub(\Magento\Framework\Cache\LockGuardedCacheLoader::class);

        $this->assertNull($plugin->beforeLockedLoadData($subject, 'CONFIG_DATA', 'time', 'time', 'time'));
        $this->assertNull(
            $plugin->beforeLockedLoadData($subject, AbstractBlock::CACHE_KEY_PREFIX . 'X', 'time', 'time', 'time')
        );
    }

    /**
     * Returns the loader that the plugin passes to the cache loader instead of the loader of the block.
     *
     * @param Cache $cache
     * @param Closure $blockLoader
     *
     * @return callable
     */
    private function wrapLoader(Cache $cache, Closure $blockLoader): callable
    {
        $plugin = new LockGuardedCacheLoader($this->createHelper(), $cache);

        $arguments = $plugin->beforeLockedLoadData(
            $this->createStub(\Magento\Framework\Cache\LockGuardedCacheLoader::class),
            AbstractBlock::CACHE_KEY_PREFIX . 'HEADER',
            $blockLoader,
            'time',
            'time'
        );

        $this->assertIsArray($arguments);
        $this->assertNotSame($blockLoader, $arguments[ 1 ]);

        return $arguments[ 1 ];
    }

    /**
     * Returns a loader that is a closure of a block, like the one of AbstractBlock::_loadCache().
     *
     * @param string|false $cachedData
     *
     * @return Closure
     */
    private function createBlockLoader(string|false $cachedData): Closure
    {
        $layout = $this->createStub(LayoutInterface::class);
        $layout->method('getParentName')->willReturn(false);

        $block = $this->createStub(AbstractBlock::class);
        $block->method('getNameInLayout')->willReturn('header');
        $block->method('getLayout')->willReturn($layout);

        // not static: the closure is bound to the block, like the closures of blocks
        return Closure::bind(fn(): string|false => $cachedData, $block, AbstractBlock::class);
    }

    /**
     * Returns the configuration with collection of blocks enabled.
     *
     * @return Data
     */
    private function createHelper(): Data
    {
        $helper = $this->createStub(Data::class);
        $helper->method('isBlocksEnabled')->willReturn(true);

        return $helper;
    }
}
