<?php

namespace Infrangible\CacheUsage\Plugin\Framework\Cache;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\AbstractBlock;
use ReflectionException;
use ReflectionFunction;
use Infrangible\CacheUsage\Model\Cache;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class LockGuardedCacheLoader
{
    /** @var Cache */
    protected $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param \Magento\Framework\Cache\LockGuardedCacheLoader $subject
     * @param string                                          $lockName
     * @param callable                                        $dataLoader
     * @param callable                                        $dataCollector
     * @param callable                                        $dataSaver
     *
     * @return array
     * @throws LocalizedException
     * @noinspection PhpUnusedParameterInspection
     */
    public function beforeLockedLoadData(
        \Magento\Framework\Cache\LockGuardedCacheLoader $subject,
        string $lockName,
        callable $dataLoader,
        callable $dataCollector,
        callable $dataSaver): array
    {
        if (preg_match(sprintf('/^%s/', AbstractBlock::CACHE_KEY_PREFIX), $lockName)) {
            try {
                $reflectionClosure = new ReflectionFunction($dataLoader);

                $closureClassInstance = $reflectionClosure->getClosureThis();

                if ($closureClassInstance instanceof AbstractBlock) {
                    $cachedData = $dataLoader();

                    if ($cachedData === false) {
                        $this->cache->addUncachedBlock($closureClassInstance);
                    } else {
                        $this->cache->addCachedBlock($closureClassInstance);
                    }
                }
            } catch (ReflectionException $exception) {
            }
        }

        return [$lockName, $dataLoader, $dataCollector, $dataSaver];
    }
}
