<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Observer;

use Exception;
use Infrangible\CacheUsage\Helper\Data;
use Infrangible\CacheUsage\Model\Cache;
use Infrangible\CacheUsage\Model\Recorder;
use Infrangible\CacheUsage\Model\Summary;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Stores the cache usage of the page and its blocks, and adds the summaries to the page, if configured. Only for
 * successful GET and HEAD requests and pages that were not found, like the full page cache.
 */
class ControllerFrontSendResponseBefore implements ObserverInterface
{
    /**
     * @param Data $helper
     * @param Cache $cache
     * @param Recorder $recorder
     * @param Summary $summary
     * @param LoggerInterface $logging
     */
    public function __construct(
        protected Data $helper,
        protected Cache $cache,
        protected Recorder $recorder,
        protected Summary $summary,
        protected LoggerInterface $logging
    ) {
    }

    /**
     * Stores the cache usage and adds the summaries to the page.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $request = $observer->getEvent()->getData('request');
        $response = $observer->getEvent()->getData('response');

        if (! $request instanceof Http || ! $response instanceof HttpResponse ||
            ! in_array($response->getHttpResponseCode(), [200, 404], true) ||
            ! ($request->isGet() || $request->isHead())) {

            return;
        }

        $duration = $this->cache->getDuration() ?? $this->getRequestDuration($request);

        // the cache usage is collected for analysis, a failure must not break the page
        if ($this->helper->isFullPageCacheEnabled()) {
            try {
                $this->recorder->saveFullPageCache($duration);
            } catch (Exception $exception) {
                $this->logging->error(sprintf('Could not store the usage of the page: %s', $exception->getMessage()));
            }
        }

        if ($this->helper->isBlocksEnabled() && ! $this->cache->isCached()) {
            try {
                $this->recorder->saveBlocks();
            } catch (Exception $exception) {
                $this->logging->error(
                    sprintf('Could not store the usage of the blocks: %s', $exception->getMessage())
                );
            }
        }

        if ($request->isAjax() || ! $this->isHtml($response)) {
            return;
        }

        $summary = $this->summary->getHtml($duration);

        if ($summary !== '') {
            $content = $response->getContent();

            $response->setContent((is_string($content) ? $content : '') . $summary);
        }
    }

    /**
     * Returns the time since the start of the request in milliseconds.
     *
     * For requests that the full page cache did not measure: full page cache disabled or Varnish.
     *
     * @param Http $request
     *
     * @return int|null
     */
    protected function getRequestDuration(Http $request): ?int
    {
        $requestTime = $request->getServer('REQUEST_TIME_FLOAT');

        return is_numeric($requestTime) ? max(0, (int)round((microtime(true) - (float)$requestTime) * 1000)) : null;
    }

    /**
     * Checks if the response is an HTML page, so that the summaries can be added.
     *
     * @param HttpResponse $response
     *
     * @return bool
     */
    protected function isHtml(HttpResponse $response): bool
    {
        $contentType = $response->getHeader('Content-Type');

        // without Content-Type header, PHP sends text/html
        return ! is_object($contentType) || str_contains(strtolower($contentType->getFieldValue()), 'text/html');
    }
}
