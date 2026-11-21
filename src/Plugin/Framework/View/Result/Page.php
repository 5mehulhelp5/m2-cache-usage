<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Plugin\Framework\View\Result;

use Infrangible\CacheUsage\Helper\Data;
use Infrangible\CacheUsage\Model\Summary;
use Magento\Framework\App\Response\HttpInterface;

/**
 * Adds the layout handles and the layout XML of the page below the page, if configured.
 */
class Page
{
    /**
     * @param Data $helper
     * @param Summary $summary
     */
    public function __construct(protected Data $helper, protected Summary $summary)
    {
    }

    /**
     * Adds the layout handles and the layout XML after the page.
     *
     * @param \Magento\Framework\View\Result\Page $subject
     * @param \Magento\Framework\View\Result\Page $result
     * @param HttpInterface $response
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function afterRenderResult(
        \Magento\Framework\View\Result\Page $subject,
        \Magento\Framework\View\Result\Page $result,
        HttpInterface $response
    ): \Magento\Framework\View\Result\Page {
        if ($this->helper->isShowHandles()) {
            $response->appendBody(
                $this->summary->getSection(
                    __('Layout Handles')->render(),
                    $subject->getLayout()->getUpdate()->getHandles()
                )
            );
        }

        if ($this->helper->isShowXml()) {
            $response->appendBody(
                $this->summary->getSection(__('Layout XML')->render(), [$this->getLayoutXml($subject)])
            );
        }

        return $result;
    }

    /**
     * Returns the merged layout updates of the files of the page, formatted.
     *
     * @param \Magento\Framework\View\Result\Page $page
     *
     * @return string
     */
    protected function getLayoutXml(\Magento\Framework\View\Result\Page $page): string
    {
        return $page->getLayout()->getUpdate()->getFileLayoutUpdatesXml()->asNiceXml();
    }
}
