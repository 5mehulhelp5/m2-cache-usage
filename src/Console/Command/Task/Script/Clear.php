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
 * Runs the task that clears all collected cache usage on the console.
 */
class Clear extends Task
{
    use \Infrangible\CacheUsage\Traits\Clear;
}
