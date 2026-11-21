<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Test\Unit\Model;

use Infrangible\CacheUsage\Model\Cache;
use Infrangible\CacheUsage\Model\Cache\Block;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests the cache usage of the request: blocks, their states and the data stored with the page.
 */
class CacheTest extends TestCase
{
    /**
     * Tests that the path in the layout consists of the names of the parents, also with a cycle in the layout.
     *
     * @return void
     */
    public function testAddBlockDataWithLayoutPath(): void
    {
        $layout = $this->createStub(LayoutInterface::class);
        $layout->method('getParentName')->willReturnMap(
            [
                ['product.info', 'content'],
                ['content', 'main'],
                ['main', 'root'],
                ['root', false],
                ['cycle.a', 'cycle.b'],
                ['cycle.b', 'cycle.a']
            ]
        );

        $cache = new Cache();

        $block = $cache->addBlockData($layout, 'product.info', 'Class', 'template.phtml');

        $this->assertSame('root/main/content/product.info', $block->getLayoutName());
        $this->assertSame('cycle.b/cycle.a', $cache->addBlockData($layout, 'cycle.a', 'Class', '-')->getLayoutName());
        $this->assertSame($block, $cache->getBlock('product.info'));
    }

    /**
     * Tests that adding a block again keeps its cache state.
     *
     * @return void
     */
    public function testAddBlockDataKeepsState(): void
    {
        $layout = $this->createStub(LayoutInterface::class);
        $layout->method('getParentName')->willReturn(false);

        $cache = new Cache();

        $cache->addBlockData($layout, 'header', 'Class', '-')->setUncacheable(true);

        $this->assertTrue($cache->addBlockData($layout, 'header', 'Other', '-')->isUncacheable());
        $this->assertSame('Other', $cache->getBlock('header')?->getClassName());
    }

    /**
     * Tests the blocks by cache state, sorted by their path in the layout.
     *
     * @return void
     */
    public function testGetBlocksByState(): void
    {
        $cache = new Cache();

        $cache->setBlocksData(
            [
                'b'      => ['layout_name' => 'root/b', 'cached' => true],
                'a'      => ['layout_name' => 'root/a', 'cached' => true],
                'menu'   => ['layout_name' => 'root/menu', 'uncacheable' => true, 'cached' => true],
                'footer' => ['layout_name' => 'root/footer', 'uncached' => true],
                'text'   => ['layout_name' => 'root/text'],
                'broken' => 'no array'
            ]
        );

        $this->assertSame(['root/a', 'root/b'], array_keys($cache->getBlocksByState(Block::STATE_CACHED)));
        $this->assertSame(['root/menu'], array_keys($cache->getBlocksByState(Block::STATE_UNCACHEABLE)));
        $this->assertSame(['root/footer'], array_keys($cache->getBlocksByState(Block::STATE_UNCACHED)));
        $this->assertSame(['root/text'], array_keys($cache->getBlocksByState(Block::STATE_DEFAULT)));
        $this->assertCount(5, $cache->getBlocks());
    }

    /**
     * Tests that the data stored with the page restores the request and its blocks, and that invalid data is ignored.
     *
     * @return void
     */
    public function testPageCacheData(): void
    {
        $layout = $this->createStub(LayoutInterface::class);
        $layout->method('getParentName')->willReturn(false);

        $cache = new Cache();

        $cache->setRouteName('catalog');
        $cache->setControllerName('product');
        $cache->setActionName('view');
        $cache->setPathParameters('id=5');
        $cache->setQueryParameters('');
        $cache->addBlockData($layout, 'header', 'Header', 'header.phtml')->setCached(true);

        $restored = new Cache();

        $restored->setPageCacheData(json_decode((string)json_encode($cache->getPageCacheData()), true));

        $this->assertSame('catalog', $restored->getRouteName());
        $this->assertSame('product', $restored->getControllerName());
        $this->assertSame('view', $restored->getActionName());
        $this->assertSame('id=5', $restored->getPathParameters());
        $this->assertSame('', $restored->getQueryParameters());
        $this->assertSame(['header'], array_keys($restored->getBlocksByState(Block::STATE_CACHED)));
        $this->assertSame('header.phtml', $restored->getBlock('header')?->getTemplateName());

        $restored->setPageCacheData('invalid');
        $restored->setPageCacheData(['route' => ['no string']]);

        $this->assertNull($restored->getRouteName());
    }

    /**
     * Tests that blocks without name get a name from their class and data, which is the same for the same data.
     *
     * @return void
     */
    public function testGenerateBlockName(): void
    {
        $block = $this->createStub(AbstractBlock::class);
        $block->method('getData')->willReturn(['b' => 2, 'a' => 1, 'object' => new \stdClass()]);
        $block->method('getNameInLayout')->willReturn(null);

        $otherBlock = $this->createStub(AbstractBlock::class);
        $otherBlock->method('getData')->willReturn(['a' => 1, 'b' => 2]);

        $cache = new Cache();

        $this->assertMatchesRegularExpression('/^anonymous\/[0-9a-f]{64}$/', $cache->getBlockName($block));
        $this->assertSame($cache->generateBlockName($block), $cache->generateBlockName($otherBlock));
    }

    /**
     * Tests the duration of the request, which is only known if it was measured.
     *
     * @return void
     */
    public function testGetDuration(): void
    {
        $cache = new Cache();

        $this->assertNull($cache->getDuration());

        $cache->setStarted(10.0);
        $cache->setFinished(10.25);

        $this->assertSame(250, $cache->getDuration());
    }
}
