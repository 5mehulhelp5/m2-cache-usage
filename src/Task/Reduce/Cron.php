<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Task\Reduce;

use Infrangible\CacheUsage\Traits\Reduce;
use Infrangible\Task\Cron\Base;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Cron
    extends Base
{
    use Reduce;
}
