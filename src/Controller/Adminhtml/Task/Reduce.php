<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Controller\Adminhtml\Task;

use Infrangible\Task\Controller\Adminhtml\Run;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Reduce
    extends Run
{
    use \Infrangible\CacheUsage\Traits\Reduce;

    /**
     * @return string
     */
    protected function getTaskResourceId(): string
    {
        return 'Infrangible_CacheUsage::infrangible_cache_usage';
    }
}
