<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Plugin\Framework\View\Result;

use Infrangible\Core\Helper\Stores;
use Magento\Framework\App\Response\HttpInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Page
{
    /** @var Stores */
    protected $storeHelper;

    public function __construct(Stores $storeHelper)
    {
        $this->storeHelper = $storeHelper;
    }

    public function aroundRenderResult(
        \Magento\Framework\View\Result\Page $subject,
        callable $proceed,
        HttpInterface $response
    ): \Magento\Framework\View\Result\Page {
        $result = $proceed($response);

        if ($this->storeHelper->getStoreConfigFlag('infrangible_cache_usage/layout/show_summary')) {
            $handles = $subject->getLayout()->getUpdate()->getHandles();

            $response->appendBody(
                sprintf(
                    '<pre>%s%s--------------------%s%s</pre>',
                    __('Layout Handles'),
                    PHP_EOL,
                    PHP_EOL,
                    implode(
                        '<br />',
                        $handles
                    )
                )
            );
        }

        return $result;
    }
}
