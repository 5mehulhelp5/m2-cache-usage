<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Controller\Adminhtml\BlockCache;

use Infrangible\CacheUsage\Traits\BlockCache;

/**
 * Loads the grid of the block cache usage with AJAX.
 */
class Grid extends \Infrangible\BackendWidget\Controller\Backend\Object\Grid
{
    use BlockCache;
}
