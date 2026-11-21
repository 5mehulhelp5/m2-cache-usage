<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Block\Adminhtml\FullPageCache;

use Exception;
use Infrangible\BackendWidget\Block\Grid\GroupBy;
use Infrangible\CacheUsage\Block\Adminhtml\AverageDuration;

/**
 * Grid of the usage of the full page cache by requests, also grouped.
 */
class Grid extends GroupBy
{
    use AverageDuration;

    /**
     * Adds the columns.
     *
     * @return void
     * @throws Exception
     */
    protected function prepareFields(): void
    {
        $this->addTextColumn('route', __('Route')->render());
        $this->addTextColumn('controller', __('Controller')->render());
        $this->addTextColumn('action', __('Action')->render());
        $this->addTextColumn('path', __('Path Parameters')->render());
        $this->addTextColumn('query', __('Query Parameters')->render());
        $this->addYesNoColumn('cacheable', __('Cacheable')->render());
        $this->addYesNoColumn('cached', __('Cached')->render());
        $this->addNumberColumn('duration', __('Duration (ms)')->render());
        $this->addDatetimeColumn('created_at', __('Date')->render());
    }

    /**
     * Returns the fields that are hidden by default.
     *
     * @return string[]
     */
    protected function getHiddenFieldNames(): array
    {
        return ['query'];
    }
}
