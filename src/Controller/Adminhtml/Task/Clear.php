<?php

namespace Infrangible\CacheUsage\Controller\Adminhtml\Task;

use Infrangible\Task\Controller\Adminhtml\Run;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Clear
    extends Run
{
    use \Infrangible\CacheUsage\Traits\Clear;

    /**
     * @return string
     */
    protected function getTaskResourceId(): string
    {
        return 'Infrangible_CacheUsage::infrangible_cache_usage';
    }
}
