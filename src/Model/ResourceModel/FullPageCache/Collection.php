<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Model\ResourceModel\FullPageCache;

use Infrangible\CacheUsage\Model\FullPageCache;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection of the usage of the full page cache by requests.
 */
class Collection extends AbstractCollection
{
    /**
     * Initializes the model and the resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(FullPageCache::class, \Infrangible\CacheUsage\Model\ResourceModel\FullPageCache::class);
    }
}
