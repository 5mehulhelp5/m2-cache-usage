<?php

namespace Infrangible\CacheUsage\Console\Command\Task;

use Infrangible\Task\Console\Command\Task;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Reduce
    extends Task
{
    use \Infrangible\CacheUsage\Traits\Reduce;

    /**
     * @return string
     */
    protected function getCommandDescription(): string
    {
        return 'Reduce all cache usage entries.';
    }

    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return Script\Reduce::class;
    }
}
