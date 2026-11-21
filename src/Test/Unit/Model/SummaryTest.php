<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Test\Unit\Model;

use Infrangible\CacheUsage\Helper\Data;
use Infrangible\CacheUsage\Model\Cache;
use Infrangible\CacheUsage\Model\Summary;
use Magento\Framework\Escaper;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests the summary below the page.
 */
class SummaryTest extends TestCase
{
    /**
     * Tests that the parameters of the request are escaped, so that a URL cannot add HTML to the page.
     *
     * @return void
     */
    public function testFullPageCacheSummaryIsEscaped(): void
    {
        $cache = new Cache();

        $cache->setRouteName('catalogsearch');
        $cache->setQueryParameters('q=<script>alert(1)</script>');
        $cache->setCacheable(true);

        $html = $this->createSummary($cache, true, false)->getHtml(15);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringContainsString('Route: catalogsearch', $html);
        $this->assertStringContainsString('Cacheable: Yes', $html);
        $this->assertStringContainsString('Cached: No', $html);
        $this->assertStringContainsString('Duration: 15 ms', $html);
    }

    /**
     * Tests the sections of the blocks: only states with blocks, and "FPC" as duration of blocks of a cached page.
     *
     * @return void
     */
    public function testBlocksSummary(): void
    {
        $layout = $this->createStub(LayoutInterface::class);
        $layout->method('getParentName')->willReturn(false);

        $cache = new Cache();

        $block = $cache->addBlockData($layout, 'header', 'Header', 'header.phtml');

        $block->setCached(true);
        $block->setStarted(1.0);
        $block->setFinished(1.003);

        $summary = $this->createSummary($cache, false, true);

        $html = $summary->getHtml(null);

        $this->assertStringContainsString('Cached Blocks', $html);
        $this->assertStringContainsString('header: 3 ms: Header: header.phtml', $html);
        $this->assertStringNotContainsString('Default Blocks', $html);
        $this->assertStringNotContainsString('Full Page Cache', $html);

        $cache->setCached(true);

        $this->assertStringContainsString('header: FPC: Header', $summary->getHtml(null));
    }

    /**
     * Tests that nothing is added without configured summaries.
     *
     * @return void
     */
    public function testWithoutSummary(): void
    {
        $this->assertSame('', $this->createSummary(new Cache(), false, false)->getHtml(10));
    }

    /**
     * Returns the summary with the configuration of the summaries.
     *
     * @param Cache $cache
     * @param bool $fullPageCacheSummary
     * @param bool $blocksSummary
     *
     * @return Summary
     */
    private function createSummary(Cache $cache, bool $fullPageCacheSummary, bool $blocksSummary): Summary
    {
        $helper = $this->createStub(Data::class);
        $helper->method('isFullPageCacheSummary')->willReturn($fullPageCacheSummary);
        $helper->method('isBlocksSummary')->willReturn($blocksSummary);

        return new Summary($helper, $cache, new Escaper());
    }
}
