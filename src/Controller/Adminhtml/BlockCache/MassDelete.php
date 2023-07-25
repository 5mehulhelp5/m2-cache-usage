<?php

namespace Infrangible\CacheUsage\Controller\Adminhtml\BlockCache;

use Infrangible\CacheUsage\Traits\BlockCache;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class MassDelete
    extends \Infrangible\BackendWidget\Controller\Backend\Object\MassDelete
{
    use BlockCache;

    /**
     * @return string
     */
    protected function getObjectsDeletedMessage(): string
    {
        return __('Successfully deleted %s block caches.');
    }
}
