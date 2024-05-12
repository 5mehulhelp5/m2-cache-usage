<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Controller\Adminhtml\BlockCache;

use Infrangible\CacheUsage\Traits\BlockCache;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Grid
    extends \Infrangible\BackendWidget\Controller\Backend\Object\Grid
{
    use BlockCache;
}
