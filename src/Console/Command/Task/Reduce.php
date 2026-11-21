<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Console\Command\Task;

use Infrangible\Task\Console\Command\Task;

/**
 * The command task:cache_usage_reduce, which removes the collected cache usage older than 30 days.
 */
class Reduce extends Task
{
    use \Infrangible\CacheUsage\Traits\Reduce;

    /**
     * Returns the class of the script that runs the task.
     *
     * @return string
     */
    protected function getClassName(): string
    {
        return Script\Reduce::class;
    }

    /**
     * Returns the description of the command.
     *
     * @return string
     */
    protected function getCommandDescription(): string
    {
        return 'Remove the collected cache usage older than 30 days.';
    }
}
