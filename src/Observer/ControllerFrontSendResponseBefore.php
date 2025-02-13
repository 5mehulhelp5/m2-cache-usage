<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Observer;

use Infrangible\CacheUsage\Model\BlockCacheFactory;
use Infrangible\CacheUsage\Model\Cache;
use Infrangible\CacheUsage\Model\FullPageCacheFactory;
use Infrangible\Core\Helper\Stores;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ControllerFrontSendResponseBefore
    implements ObserverInterface
{
    /** @var Stores */
    protected $storeHelper;

    /** @var Cache */
    protected $cache;

    /** @var FullPageCacheFactory */
    protected $fullPageCacheFactory;

    /** @var \Infrangible\CacheUsage\Model\ResourceModel\FullPageCacheFactory */
    protected $fullPageCacheResourceFactory;

    /** @var BlockCacheFactory */
    protected $blockCacheFactory;

    /** @var \Infrangible\CacheUsage\Model\ResourceModel\BlockCacheFactory */
    protected $blockCacheResourceFactory;

    public function __construct(
        Stores $storeHelper,
        Cache $cache,
        FullPageCacheFactory $fullPageCacheFactory,
        \Infrangible\CacheUsage\Model\ResourceModel\FullPageCacheFactory $fullPageCacheResourceFactory,
        BlockCacheFactory $blockCacheFactory,
        \Infrangible\CacheUsage\Model\ResourceModel\BlockCacheFactory $blockCacheResourceFactory
    ) {
        $this->storeHelper = $storeHelper;

        $this->cache = $cache;
        $this->fullPageCacheFactory = $fullPageCacheFactory;
        $this->fullPageCacheResourceFactory = $fullPageCacheResourceFactory;
        $this->blockCacheFactory = $blockCacheFactory;
        $this->blockCacheResourceFactory = $blockCacheResourceFactory;
    }

    /***
     * @throws AlreadyExistsException
     */
    public function execute(Observer $observer): void
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $observer->getEvent()->getData('request');

        /** @var Http $response */
        $response = $observer->getEvent()->getData('response');

        $httpResponseCode = $response->getHttpResponseCode();

        if (($httpResponseCode == 200 || $httpResponseCode == 404) && ($request->isGet() || $request->isHead())) {
            $content = $response->getContent();

            if ($this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/fpc/enable')) {
                $fullPageCache = $this->fullPageCacheFactory->create();

                $fullPageCache->setRoute($this->cache->getRouteName());
                $fullPageCache->setController($this->cache->getControllerName());
                $fullPageCache->setAction($this->cache->getActionName());
                $fullPageCache->setPath($this->cache->getPathParameters());
                $fullPageCache->setQuery($this->cache->getQueryParameters());
                $fullPageCache->setCacheable((int) $this->cache->isCacheable());
                $fullPageCache->setCached((int) $this->cache->isCached());
                $fullPageCache->setDuration((int) $this->cache->getDuration());

                $this->fullPageCacheResourceFactory->create()->save($fullPageCache);
            }

            if ($this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/blocks/enable')) {
                if (!$this->cache->isCached()) {
                    $blockCacheResource = $this->blockCacheResourceFactory->create();

                    $blocks = $this->cache->getBlocks();

                    if (count($blocks)) {
                        foreach ($blocks as $block) {
                            $blockCache = $this->blockCacheFactory->create();

                            $blockCache->setRoute($this->cache->getRouteName());
                            $blockCache->setController($this->cache->getControllerName());
                            $blockCache->setAction($this->cache->getActionName());
                            $blockCache->setPath($this->cache->getPathParameters());
                            $blockCache->setQuery($this->cache->getQueryParameters());
                            $blockCache->setLayoutName($block->getLayoutName());
                            $blockCache->setClassName($block->getClassName());
                            $blockCache->setTemplateName($block->getTemplateName());
                            $blockCache->setDuration($block->getDuration());

                            if ($block->isUncacheable()) {
                                $blockCache->setCacheable(0);
                                $blockCache->setCached(0);
                            } elseif ($block->isCached()) {
                                $blockCache->setCacheable(1);
                                $blockCache->setCached(1);
                            } elseif ($block->isUncached()) {
                                $blockCache->setCacheable(1);
                                $blockCache->setCached(0);
                            } else {
                                $blockCache->setCacheable(2);
                                $blockCache->setCached(0);
                            }

                            $blockCacheResource->save($blockCache);
                        }
                    }
                }
            }

            if (!$request->isAjax() && $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/fpc/enable')
                && $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/fpc/show_summary')) {

                $content .= '<pre>';
                $content .= sprintf("%s\n--------------------\n", __('Full Page Cache'));
                $content .= sprintf("Route: %s\n", $this->cache->getRouteName());
                $content .= sprintf("Controller: %s\n", $this->cache->getControllerName());
                $content .= sprintf("Action: %s\n", $this->cache->getActionName());
                $content .= sprintf("Path Parameters: %s\n", $this->cache->getPathParameters());
                $content .= sprintf("Query Parameters: %s\n", $this->cache->getQueryParameters());
                $content .= sprintf("Cacheable: %s\n", var_export($this->cache->isCacheable(), true));
                $content .= sprintf("Cached: %s\n", var_export($this->cache->isCached(), true));
                $content .= sprintf("Duration: %s ms\n", var_export($this->cache->getDuration(), true));
                $content .= '</pre>';
            }

            if (!$request->isAjax() && $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/blocks/enable')
                && $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/blocks/show_summary')) {

                $cachedBlocks = $this->cache->getCachedBlocks();

                if (count($cachedBlocks)) {
                    ksort($cachedBlocks);

                    $content .= '<pre>';
                    $content .= sprintf("%s\n--------------------\n", __('Cached Blocks'));
                    foreach ($cachedBlocks as $block) {
                        $content .= sprintf(
                            "%s: %s: %s: %s\n",
                            $block->getLayoutName(),
                            $this->cache->isCached() ? 'FPC' : sprintf('%d ms', $block->getDuration()),
                            $block->getClassName(),
                            $block->getTemplateName()
                        );
                    }
                    $content .= '</pre>';
                }

                $uncachedBlocks = $this->cache->getUncachedBlocks();

                if (count($uncachedBlocks)) {
                    ksort($uncachedBlocks);

                    $content .= '<pre>';
                    $content .= sprintf("%s\n--------------------\n", __('Uncached Blocks'));
                    foreach ($uncachedBlocks as $block) {
                        $content .= sprintf(
                            "%s: %s: %s: %s\n",
                            $block->getLayoutName(),
                            $this->cache->isCached() ? 'FPC' : $block->getDuration(),
                            $block->getClassName(),
                            $block->getTemplateName()
                        );
                    }
                    $content .= '</pre>';
                }

                $uncacheableBlocks = $this->cache->getUncacheableBlocks();

                if (count($uncacheableBlocks)) {
                    ksort($uncacheableBlocks);

                    $content .= '<pre>';
                    $content .= sprintf("%s\n--------------------\n", __('Uncacheable Blocks'));
                    foreach ($uncacheableBlocks as $block) {
                        $content .= sprintf(
                            "%s: %s: %s: %s\n",
                            $block->getLayoutName(),
                            $this->cache->isCached() ? 'FPC' : $block->getDuration(),
                            $block->getClassName(),
                            $block->getTemplateName()
                        );
                    }
                    $content .= '</pre>';
                }

                $defaultBlocks = $this->cache->getDefaultBlocks();

                if (count($defaultBlocks)) {
                    ksort($defaultBlocks);

                    $content .= '<pre>';
                    $content .= sprintf("%s\n--------------------\n", __('Default Blocks'));
                    foreach ($defaultBlocks as $block) {
                        $content .= sprintf(
                            "%s: %s: %s: %s\n",
                            $block->getLayoutName(),
                            $this->cache->isCached() ? 'FPC' : $block->getDuration(),
                            $block->getClassName(),
                            $block->getTemplateName()
                        );
                    }
                    $content .= '</pre>';
                }
            }

            $response->setContent($content);
        }
    }
}
