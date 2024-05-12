<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Plugin\Framework\View;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout\Element;
use Infrangible\CacheUsage\Model\Cache;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Layout
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
     * @param \Magento\Framework\View\Layout $subject
     */
    public function afterGenerateElements(\Magento\Framework\View\Layout $subject)
    {
        if ($subject->isCacheable()) {
            $this->cache->setCacheable(true);
        }

        $xml = $subject->getUpdate()->asSimplexml();

        $elements = $xml->xpath('//' . Element::TYPE_BLOCK . '[@cacheable="false"]');

        if (count($elements)) {
            /** @var Element $element */
            foreach ($elements as $element) {
                $blockName = $element->getBlockName();
                $className = $element->getAttribute('class');

                $templateName = '-';

                if ($subject->isBlock($blockName)) {
                    $block = $subject->getBlock($blockName);

                    if ($block instanceof Template) {
                        $template = $block->getTemplate();

                        if ( ! empty($template)) {
                            $templateName = $block->getTemplateFile();
                        }
                    }
                }

                $cacheBlock = $this->cache->addBlockData($subject, $blockName, $className, $templateName);

                $cacheBlock->setUncacheable(true);
            }
        }
    }
}
