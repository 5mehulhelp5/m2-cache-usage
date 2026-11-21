<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Task\Reduce;

use Infrangible\CacheUsage\Traits\Reduce;
use Infrangible\Task\Cron\Base;

/**
 * The cron job task_cache_usage_reduce, which removes the collected cache usage older than 30 days.
 */
class Cron extends Base
{
    use Reduce;
}
