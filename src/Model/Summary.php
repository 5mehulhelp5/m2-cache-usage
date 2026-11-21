<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Model;

use Infrangible\CacheUsage\Helper\Data;
use Infrangible\CacheUsage\Model\Cache\Block;
use Magento\Framework\Escaper;

/**
 * The summary of the cache usage of the request that is shown below the page, if configured.
 */
class Summary
{
    /**
     * @param Data $helper
     * @param Cache $cache
     * @param Escaper $escaper
     */
    public function __construct(protected Data $helper, protected Cache $cache, protected Escaper $escaper)
    {
    }

    /**
     * Returns the HTML of the configured summaries of the full page cache and of the blocks.
     *
     * @param int|null $duration the duration of the request in milliseconds
     *
     * @return string
     */
    public function getHtml(?int $duration): string
    {
        $html = '';

        if ($this->helper->isFullPageCacheSummary()) {
            $html .= $this->getSection(
                __('Full Page Cache')->render(),
                [
                    sprintf('%s: %s', __('Route')->render(), $this->cache->getRouteName() ?? '-'),
                    sprintf('%s: %s', __('Controller')->render(), $this->cache->getControllerName() ?? '-'),
                    sprintf('%s: %s', __('Action')->render(), $this->cache->getActionName() ?? '-'),
                    sprintf('%s: %s', __('Path Parameters')->render(), $this->cache->getPathParameters() ?? '-'),
                    sprintf('%s: %s', __('Query Parameters')->render(), $this->cache->getQueryParameters() ?? '-'),
                    sprintf('%s: %s', __('Cacheable')->render(), $this->getYesNo($this->cache->isCacheable())),
                    sprintf('%s: %s', __('Cached')->render(), $this->getYesNo($this->cache->isCached())),
                    sprintf('%s: %s', __('Duration')->render(), $this->getDuration($duration))
                ]
            );
        }

        if ($this->helper->isBlocksSummary()) {
            $sections = [
                Block::STATE_CACHED      => __('Cached Blocks')->render(),
                Block::STATE_UNCACHED    => __('Uncached Blocks')->render(),
                Block::STATE_UNCACHEABLE => __('Uncacheable Blocks')->render(),
                Block::STATE_DEFAULT     => __('Default Blocks')->render()
            ];

            foreach ($sections as $state => $title) {
                $blocks = $this->cache->getBlocksByState($state);

                if (count($blocks) > 0) {
                    $html .= $this->getSection($title, array_map($this->getBlockLine(...), $blocks));
                }
            }
        }

        return $html;
    }

    /**
     * Returns the line of the block in the summary: its path in the layout, duration, class and template.
     *
     * @param Block $block
     *
     * @return string
     */
    protected function getBlockLine(Block $block): string
    {
        return sprintf(
            '%s: %s: %s: %s',
            $block->getLayoutName() ?? '-',
            $this->cache->isCached() ? 'FPC' : $this->getDuration($block->getDuration()),
            $block->getClassName() ?? '-',
            $block->getTemplateName() ?? '-'
        );
    }

    /**
     * Returns a section of the summary with title and escaped lines.
     *
     * @param string $title
     * @param string[] $lines
     *
     * @return string
     */
    public function getSection(string $title, array $lines): string
    {
        $escapedLines = array_map(
            fn(string $line): string => $this->escaper->escapeHtml($line),
            array_values($lines)
        );

        return sprintf(
            "<pre class=\"cache-usage-summary\">%s\n--------------------\n%s\n</pre>",
            $this->escaper->escapeHtml($title),
            implode("\n", $escapedLines)
        );
    }

    /**
     * Returns the duration in milliseconds as text.
     *
     * @param int|null $duration
     *
     * @return string
     */
    protected function getDuration(?int $duration): string
    {
        return $duration === null ? '-' : sprintf('%d ms', $duration);
    }

    /**
     * Returns "Yes" or "No".
     *
     * @param bool $value
     *
     * @return string
     */
    protected function getYesNo(bool $value): string
    {
        return $value ? __('Yes')->render() : __('No')->render();
    }
}
