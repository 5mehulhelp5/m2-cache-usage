<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Controller\Adminhtml\FullPageCache;

use Infrangible\CacheUsage\Traits\FullPageCache;

/**
 * Deletes the selected entries of the full page cache usage.
 */
class MassDelete extends \Infrangible\BackendWidget\Controller\Backend\Object\MassDelete
{
    use FullPageCache;

    /**
     * Returns the message for the deleted entries, with %s for their number.
     *
     * @return string
     */
    protected function getObjectsDeletedMessage(): string
    {
        return __('Deleted %s full page cache entries.')->render();
    }
}
