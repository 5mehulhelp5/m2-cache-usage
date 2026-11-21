<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Plugin\PageCache\Model\Cache;

use Infrangible\CacheUsage\Helper\Data;
use Infrangible\CacheUsage\Model\Cache;
use Infrangible\CacheUsage\Plugin\Framework\App\PageCache\Kernel;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * Stores the data of the request (route and blocks) next to the page in the full page cache, with the identifier, tags
 * and lifetime of the page, and loads it with the page. The identifier is the one that Magento uses: since 2.4.7, the
 * page is stored with another identifier than it is loaded with.
 */
class Type
{
    /** Suffix of the identifier of the data of the request. */
    public const IDENTIFIER_SUFFIX = '_cache_usage';

    /**
     * @param Data $helper
     * @param Cache $cache
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logging
     */
    public function __construct(
        protected Data $helper,
        protected Cache $cache,
        protected SerializerInterface $serializer,
        protected LoggerInterface $logging
    ) {
    }

    /**
     * Stores the data of the request when the kernel stores a page.
     *
     * @param \Magento\PageCache\Model\Cache\Type $subject
     * @param bool $result
     * @param mixed $data
     * @param string $identifier
     * @param string[] $tags
     * @param mixed $lifeTime the lifetime in seconds, the kernel passes the max age of the page as string
     *
     * @return bool
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterSave(
        \Magento\PageCache\Model\Cache\Type $subject,
        bool $result,
        mixed $data,
        string $identifier,
        array $tags = [],
        mixed $lifeTime = null
    ): bool {
        if ($result && $this->isPageOperation(Kernel::OPERATION_PROCESS, $identifier) &&
            $this->helper->isCollecting()) {

            // the operation is reset first, so that the plugin does not handle its own save
            $this->cache->setPageCacheOperation(null);

            $pageCacheData = $this->serializer->serialize($this->cache->getPageCacheData());

            if (is_string($pageCacheData)) {
                $subject->save(
                    $pageCacheData,
                    $identifier . self::IDENTIFIER_SUFFIX,
                    $tags,
                    is_numeric($lifeTime) ? (int)$lifeTime : null
                );
            }
        }

        return $result;
    }

    /**
     * Loads the data of the request when the kernel loaded a page.
     *
     * @param \Magento\PageCache\Model\Cache\Type $subject
     * @param mixed $result the cached data, or false
     * @param string $identifier
     *
     * @return mixed
     */
    public function afterLoad(\Magento\PageCache\Model\Cache\Type $subject, mixed $result, string $identifier): mixed
    {
        if ($result === false || ! $this->isPageOperation(Kernel::OPERATION_LOAD, $identifier) ||
            ! $this->helper->isCollecting()) {

            return $result;
        }

        $this->cache->setPageCacheOperation(null);

        $data = $subject->load($identifier . self::IDENTIFIER_SUFFIX);

        if (is_string($data) && $data !== '') {
            try {
                $this->cache->setPageCacheData($this->serializer->unserialize($data));
            } catch (\InvalidArgumentException $exception) {
                $this->logging->warning(
                    sprintf('Could not read the cache usage of the cached page: %s', $exception->getMessage())
                );
            }
        }

        return $result;
    }

    /**
     * Checks if the page cache kernel runs the operation and the identifier is the one of a page.
     *
     * @param string $operation
     * @param string $identifier
     *
     * @return bool
     */
    protected function isPageOperation(string $operation, string $identifier): bool
    {
        return $this->cache->getPageCacheOperation() === $operation &&
            ! str_ends_with($identifier, self::IDENTIFIER_SUFFIX);
    }
}
