<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Console\Command\Task\Script;

use Infrangible\Task\Console\Command\Script\Task;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Clear
    extends Task
{
    use \Infrangible\CacheUsage\Traits\Clear;
}
