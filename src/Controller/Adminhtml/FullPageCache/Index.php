<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Controller\Adminhtml\FullPageCache;

use Infrangible\CacheUsage\Traits\FullPageCache;

/**
 * Shows the grid of the full page cache usage.
 */
class Index extends \Infrangible\BackendWidget\Controller\Backend\Object\Index
{
    use FullPageCache;
}
