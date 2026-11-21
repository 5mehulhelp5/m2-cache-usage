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
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Finishes measuring the rendering of the block and marks it with a border of the color of its cache state, if
 * configured.
 */
class ViewBlockAbstractToHtmlAfter implements ObserverInterface
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
     * Finishes measuring the rendering of the block and marks it.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $block = $observer->getEvent()->getData('block');
        $transport = $observer->getEvent()->getData('transport');

        if (! $block instanceof AbstractBlock || ! $transport instanceof DataObject ||
            ! $this->helper->isBlocksEnabled()) {

            return;
        }

        $cacheBlock = $this->cache->getBlock($this->cache->getBlockName($block));

        if ($cacheBlock === null) {
            return;
        }

        $cacheBlock->setFinished(microtime(true));

        $state = $cacheBlock->getState();
        $html = $transport->getData('html');

        // blocks without output get no border, which would only show an empty box
        if (! is_string($html) || trim($html) === '' || ! $this->helper->isBlocksMarked($state)) {
            return;
        }

        $transport->setData(
            'html',
            sprintf(
                '<div class="block-info block-%s"><div class="block-info-details">' .
                '<span class="block-info-details-icon">i</span><span class="block-info-details-content">' .
                '%s: %s<br/>%s: %s<br/>%s: %s</span></div>%s</div>',
                $state,
                $this->escaper->escapeHtml(__('Layout')->render()),
                $this->escaper->escapeHtml((string)$cacheBlock->getLayoutName()),
                $this->escaper->escapeHtml(__('Class')->render()),
                $this->escaper->escapeHtml((string)$cacheBlock->getClassName()),
                $this->escaper->escapeHtml(__('Template')->render()),
                $this->escaper->escapeHtml((string)$cacheBlock->getTemplateName()),
                $html
            )
        );
    }
}
