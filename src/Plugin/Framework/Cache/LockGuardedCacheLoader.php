<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Plugin\Framework\Cache;

use Closure;
use Infrangible\CacheUsage\Helper\Data;
use Infrangible\CacheUsage\Model\Cache;
use Magento\Framework\View\Element\AbstractBlock;
use ReflectionException;
use ReflectionFunction;

/**
 * Records whether a block is loaded from the block cache: blocks load their cache with
 * LockGuardedCacheLoader::lockedLoadData() and a closure of the block as data loader.
 */
class LockGuardedCacheLoader
{
    /**
     * @param Data $helper
     * @param Cache $cache
     */
    public function __construct(protected Data $helper, protected Cache $cache)
    {
    }

    /**
     * Wraps the data loader of a block, so that the result of loading the block cache is recorded.
     *
     * @param \Magento\Framework\Cache\LockGuardedCacheLoader $subject
     * @param string $lockName
     * @param callable $dataLoader
     * @param callable $dataCollector
     * @param callable $dataSaver
     *
     * @return array{0: string, 1: callable, 2: callable, 3: callable}|null
     * @noinspection PhpUnusedParameterInspection
     */
    public function beforeLockedLoadData(
        \Magento\Framework\Cache\LockGuardedCacheLoader $subject,
        string $lockName,
        callable $dataLoader,
        callable $dataCollector,
        callable $dataSaver
    ): ?array {
        if (! str_starts_with($lockName, AbstractBlock::CACHE_KEY_PREFIX) || ! $this->helper->isBlocksEnabled()) {
            return null;
        }

        $block = $this->getBlock($dataLoader);

        if ($block === null) {
            return null;
        }

        $recordingDataLoader = function () use ($dataLoader, $block): mixed {
            $data = $dataLoader();

            // the loader runs again after waiting for the lock of another request, the last result counts
            $cacheBlock = $this->cache->addBlock($block);

            $cacheBlock->setCached($data !== false);
            $cacheBlock->setUncached($data === false);

            return $data;
        };

        return [$lockName, $recordingDataLoader, $dataCollector, $dataSaver];
    }

    /**
     * Returns the block whose closure the data loader is, or null.
     *
     * @param callable $dataLoader
     *
     * @return AbstractBlock|null
     */
    private function getBlock(callable $dataLoader): ?AbstractBlock
    {
        if (! $dataLoader instanceof Closure) {
            return null;
        }

        try {
            $block = (new ReflectionFunction($dataLoader))->getClosureThis();
        } catch (ReflectionException) {
            return null;
        }

        return $block instanceof AbstractBlock ? $block : null;
    }
}
