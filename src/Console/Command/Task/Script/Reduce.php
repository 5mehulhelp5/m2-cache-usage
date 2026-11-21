<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Console\Command\Task\Script;

use Infrangible\Task\Console\Command\Script\Task;

/**
 * Runs the task that removes the collected cache usage older than 30 days on the console.
 */
class Reduce extends Task
{
    use \Infrangible\CacheUsage\Traits\Reduce;
}
