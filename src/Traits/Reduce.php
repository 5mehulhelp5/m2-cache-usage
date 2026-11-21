<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Traits;

/**
 * Name and class of the task that removes the collected cache usage older than 30 days, for its controller, cron
 * job and console command.
 */
trait Reduce
{
    /**
     * Returns the name of the task, which is also the name of its configuration section (task_<name>).
     *
     * @return string
     */
    protected function getTaskName(): string
    {
        return 'cache_usage_reduce';
    }

    /**
     * Returns the class of the task.
     *
     * @return string
     */
    protected function getClassName(): string
    {
        return \Infrangible\CacheUsage\Task\Reduce::class;
    }
}
