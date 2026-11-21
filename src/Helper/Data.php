<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Helper;

use Infrangible\Core\Helper\Stores;

/**
 * The configuration of the cache usage in the store of the request.
 */
class Data
{
    /**
     * @param Stores $storeHelper
     */
    public function __construct(protected Stores $storeHelper)
    {
    }

    /**
     * Checks if the usage of the full page cache is collected.
     *
     * @return bool
     */
    public function isFullPageCacheEnabled(): bool
    {
        return $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/fpc/enable');
    }

    /**
     * Checks if the summary of the full page cache is shown below the page.
     *
     * @return bool
     */
    public function isFullPageCacheSummary(): bool
    {
        return $this->isFullPageCacheEnabled() &&
            $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/fpc/show_summary');
    }

    /**
     * Checks if the usage of the block cache is collected.
     *
     * @return bool
     */
    public function isBlocksEnabled(): bool
    {
        return $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/blocks/enable');
    }

    /**
     * Checks if the summary of the blocks is shown below the page.
     *
     * @return bool
     */
    public function isBlocksSummary(): bool
    {
        return $this->isBlocksEnabled() &&
            $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/blocks/show_summary');
    }

    /**
     * Checks if the blocks with the cache state are marked with a border.
     *
     * @param string $state one of the STATE constants of the block
     *
     * @return bool
     */
    public function isBlocksMarked(string $state): bool
    {
        return $this->isBlocksEnabled() &&
            $this->storeHelper->getStoreConfigFlag(sprintf('infrangible_cache_usage/blocks/mark_%s', $state));
    }

    /**
     * Checks if the layout handles are shown below the page.
     *
     * @return bool
     */
    public function isShowHandles(): bool
    {
        return $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/layout/show_handles');
    }

    /**
     * Checks if the layout XML is shown below the page.
     *
     * @return bool
     */
    public function isShowXml(): bool
    {
        return $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/layout/show_xml');
    }

    /**
     * Checks if the full page cache or the blocks are collected, so that the data of the request is needed.
     *
     * @return bool
     */
    public function isCollecting(): bool
    {
        return $this->isFullPageCacheEnabled() || $this->isBlocksEnabled();
    }
}
