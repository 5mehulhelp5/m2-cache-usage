<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Observer;

use Infrangible\CacheUsage\Helper\Data;
use Infrangible\CacheUsage\Model\Cache;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Adds every block before it is rendered and starts measuring its rendering. Whether the block is loaded from the
 * block cache is known only afterwards (plugin of LockGuardedCacheLoader).
 */
class ViewBlockAbstractToHtmlBefore implements ObserverInterface
{
    /**
     * @param Data $helper
     * @param Cache $cache
     */
    public function __construct(protected Data $helper, protected Cache $cache)
    {
    }

    /**
     * Adds the block and starts measuring its rendering.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $block = $observer->getEvent()->getData('block');

        if ($block instanceof AbstractBlock && $this->helper->isBlocksEnabled()) {
            $this->cache->startBlock($block);
        }
    }
}
