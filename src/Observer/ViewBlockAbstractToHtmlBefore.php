<?php

namespace Infrangible\CacheUsage\Observer;

use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\AbstractBlock;
use Infrangible\CacheUsage\Model\Cache;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ViewBlockAbstractToHtmlBefore
    implements ObserverInterface
{
    /** @var StateInterface */
    protected $cacheState;

    /** @var Cache */
    protected $cache;

    /**
     * @param StateInterface $cacheState
     * @param Cache          $cache
     */
    public function __construct(StateInterface $cacheState, Cache $cache)
    {
        $this->cacheState = $cacheState;
        $this->cache = $cache;
    }

    /**
     * @param Observer $observer
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var AbstractBlock $block */
        $block = $observer->getEvent()->getData('block');

        $cacheLifetime = $block->getDataUsingMethod('cache_lifetime');

        if ($cacheLifetime === null || ! $this->cacheState->isEnabled(AbstractBlock::CACHE_GROUP)) {
            $this->cache->addBlock($block);
        }
    }
}
