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
 * The command task:cache_usage_clear, which clears all collected cache usage.
 */
class Clear extends Task
{
    use \Infrangible\CacheUsage\Traits\Clear;

    /**
     * Returns the class of the script that runs the task.
     *
     * @return string
     */
    protected function getClassName(): string
    {
        return Script\Clear::class;
    }

    /**
     * Returns the description of the command.
     *
     * @return string
     */
    protected function getCommandDescription(): string
    {
        return 'Clear all collected cache usage.';
    }
}
