<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Plugin\Framework\View;

use Infrangible\CacheUsage\Helper\Data;
use Infrangible\CacheUsage\Model\Cache;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Layout\Element;

/**
 * Records whether the page is cacheable, and the blocks that the layout declares as not cacheable, which make the
 * whole page uncacheable.
 */
class Layout
{
    /**
     * @param Data $helper
     * @param Cache $cache
     */
    public function __construct(protected Data $helper, protected Cache $cache)
    {
    }

    /**
     * Records whether the page is cacheable and its blocks with cacheable="false".
     *
     * @param \Magento\Framework\View\Layout $subject
     *
     * @return void
     */
    public function afterGenerateElements(\Magento\Framework\View\Layout $subject): void
    {
        if (! $this->helper->isCollecting()) {
            return;
        }

        if ($subject->isCacheable()) {
            $this->cache->setCacheable(true);
        }

        if (! $this->helper->isBlocksEnabled()) {
            return;
        }

        $elements = $subject->getUpdate()->asSimplexml()->xpath(
            sprintf('//%s[@cacheable="false"]', Element::TYPE_BLOCK)
        );

        foreach ($elements as $element) {
            if (! $element instanceof Element) {
                continue;
            }

            $blockName = $element->getBlockName();

            if (! is_string($blockName) || $blockName === '') {
                continue;
            }

            $className = $element->getAttribute('class');
            $block = $subject->getBlock($blockName);

            $cacheBlock = $this->cache->addBlockData(
                $subject,
                $blockName,
                is_string($className) && $className !== '' ? $className : AbstractBlock::class,
                $block instanceof AbstractBlock ? $this->cache->getTemplateName($block) : '-'
            );

            $cacheBlock->setUncacheable(true);
        }
    }
}
