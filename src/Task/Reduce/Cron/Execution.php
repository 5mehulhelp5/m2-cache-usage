<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Task\Reduce\Cron;

use Infrangible\CacheUsage\Traits\Reduce;
use Infrangible\Task\Cron\Execution\Base;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Execution
    extends Base
{
    use Reduce;
}
