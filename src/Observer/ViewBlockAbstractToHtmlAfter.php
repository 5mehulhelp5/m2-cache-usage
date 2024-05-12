<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Observer;

use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Infrangible\CacheUsage\Model\Cache;
use Infrangible\Core\Helper\Stores;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ViewBlockAbstractToHtmlAfter
    implements ObserverInterface
{
    /** @var Stores */
    protected $storeHelper;

    /** @var StateInterface */
    protected $cacheState;

    /** @var Cache */
    protected $cache;

    /**
     * @param Stores         $storeHelper
     * @param StateInterface $cacheState
     * @param Cache          $cache
     */
    public function __construct(Stores $storeHelper, StateInterface $cacheState, Cache $cache)
    {
        $this->storeHelper = $storeHelper;
        $this->cacheState = $cacheState;
        $this->cache = $cache;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->cacheState->isEnabled(AbstractBlock::CACHE_GROUP)) {
            /** @var AbstractBlock $block */
            $block = $observer->getEvent()->getData('block');

            /** @var DataObject $transport */
            $transport = $observer->getEvent()->getData('transport');

            $html = $transport->getData('html');

            $blockName = $block->getNameInLayout();

            if ($blockName === null) {
                $blockName = $this->cache->generateBlockName($block);
            }

            $cacheBlock = $this->cache->getBlock($blockName);

            $cacheBlock->setFinished(microtime(true));

            $borderClass = null;

            if ($cacheBlock->isUncacheable() &&
                $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/blocks/mark_uncacheable')) {
                $borderClass = 'block-uncacheable';
            } else if ($cacheBlock->isCached() &&
                $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/blocks/mark_cached')) {
                $borderClass = 'block-cached';
            } else if ($cacheBlock->isUncached() &&
                $this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/blocks/mark_uncached')) {
                $borderClass = 'block-uncached';
            } else if ($this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/blocks/mark_default')) {
                $borderClass = 'block-default';
            }

            if ($borderClass) {
                $html = sprintf('
<div class="block-info %s">
<div class="block-info-details">
    <a href="#" class="block-info-details-icon">
        <!--suppress HtmlRequiredAltAttribute, RequiredAttributes -->
        <img>
    </a>
    <span class="block-info-details-content">Layout: %s<br/>Class: %s<br/>Template: %s</span>
</div>
%s
</div>', $borderClass, $cacheBlock->getLayoutName(), $cacheBlock->getClassName(), $cacheBlock->getTemplateName(),
                    $html);
            }

            $transport->setData('html', $html);
        }
    }
}
