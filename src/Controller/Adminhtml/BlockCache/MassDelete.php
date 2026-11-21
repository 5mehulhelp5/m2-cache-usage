<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Controller\Adminhtml\BlockCache;

use Infrangible\CacheUsage\Traits\BlockCache;

/**
 * Deletes the selected entries of the block cache usage.
 */
class MassDelete extends \Infrangible\BackendWidget\Controller\Backend\Object\MassDelete
{
    use BlockCache;

    /**
     * Returns the message for the deleted entries, with %s for their number.
     *
     * @return string
     */
    protected function getObjectsDeletedMessage(): string
    {
        return __('Deleted %s block cache entries.')->render();
    }
}
