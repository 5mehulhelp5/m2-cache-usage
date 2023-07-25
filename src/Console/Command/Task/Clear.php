<?php

namespace Infrangible\CacheUsage\Console\Command\Task;

use Infrangible\Task\Console\Command\Task;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Clear
    extends Task
{
    use \Infrangible\CacheUsage\Traits\Clear;

    /**
     * @return string
     */
    protected function getCommandDescription(): string
    {
        return 'Clear all cache usage entries.';
    }

    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return Script\Clear::class;
    }
}
