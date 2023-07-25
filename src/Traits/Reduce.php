<?php

namespace Infrangible\CacheUsage\Traits;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
trait Reduce
{
    /**
     * Returns the name of the task to initialize
     *
     * @return string
     */
    protected function getTaskName(): string
    {
        return 'cache_usage_reduce';
    }

    /**
     * Returns the name of the task to initialize
     *
     * @return string
     */
    protected function getClassName(): string
    {
        return \Infrangible\CacheUsage\Task\Reduce::class;
    }
}
