<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Controller\Adminhtml\Task;

use Infrangible\Task\Controller\Adminhtml\Run;

/**
 * Runs the task that clears all collected cache usage from the menu of the admin.
 */
class Clear extends Run
{
    use \Infrangible\CacheUsage\Traits\Clear;

    /**
     * Returns the ACL resource that allows running the task.
     *
     * @return string
     */
    protected function getTaskResourceId(): string
    {
        return 'Infrangible_CacheUsage::infrangible_cache_usage';
    }
}
