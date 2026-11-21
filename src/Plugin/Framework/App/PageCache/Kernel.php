<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Plugin\Framework\App\PageCache;

use Infrangible\CacheUsage\Model\Cache;
use Magento\Framework\App\Response\Http;

/**
 * Measures loading and storing the page in the built-in full page cache, and records whether it was cached. While the
 * kernel loads or stores the page, the plugin of the full page cache type loads or stores the data of the request.
 */
class Kernel
{
    /** Operation of the kernel that loads a page. */
    public const OPERATION_LOAD = 'load';

    /** Operation of the kernel that stores a page. */
    public const OPERATION_PROCESS = 'process';

    /**
     * @param Cache $cache
     */
    public function __construct(protected Cache $cache)
    {
    }

    /**
     * Starts measuring and loads the page.
     *
     * @param \Magento\Framework\App\PageCache\Kernel $subject
     * @param callable $proceed
     *
     * @return Http|false the cached page, or false
     * @noinspection PhpUnusedParameterInspection
     */
    public function aroundLoad(\Magento\Framework\App\PageCache\Kernel $subject, callable $proceed): Http|false
    {
        $this->cache->setCacheable(false);
        $this->cache->setCached(false);
        $this->cache->setStarted(microtime(true));
        $this->cache->setPageCacheOperation(self::OPERATION_LOAD);

        try {
            $result = $proceed();
        } finally {
            $this->cache->setPageCacheOperation(null);
        }

        if ($result instanceof Http) {
            $this->cache->setCacheable(true);
            $this->cache->setCached(true);
            $this->cache->setFinished(microtime(true));

            return $result;
        }

        return false;
    }

    /**
     * Stores the page and finishes measuring.
     *
     * @param \Magento\Framework\App\PageCache\Kernel $subject
     * @param callable $proceed
     * @param Http $response
     *
     * @return void
     * @noinspection PhpUnusedParameterInspection
     */
    public function aroundProcess(
        \Magento\Framework\App\PageCache\Kernel $subject,
        callable $proceed,
        Http $response
    ): void {
        $this->cache->setPageCacheOperation(self::OPERATION_PROCESS);

        try {
            $proceed($response);
        } finally {
            $this->cache->setPageCacheOperation(null);
            $this->cache->setFinished(microtime(true));
        }
    }
}
