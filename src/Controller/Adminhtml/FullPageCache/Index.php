<?php

namespace Infrangible\CacheUsage\Controller\Adminhtml\FullPageCache;

use Infrangible\CacheUsage\Traits\FullPageCache;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Index
    extends \Infrangible\BackendWidget\Controller\Backend\Object\Index
{
    use FullPageCache;
}
