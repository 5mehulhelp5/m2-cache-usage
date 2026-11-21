<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Task\Reduce\Cron;

use Infrangible\CacheUsage\Traits\Reduce;
use Infrangible\Task\Cron\Execution\Base;

/**
 * Backend model of the cron expression of the task that reduces the cache usage in the configuration.
 */
class Execution extends Base
{
    use Reduce;
}
