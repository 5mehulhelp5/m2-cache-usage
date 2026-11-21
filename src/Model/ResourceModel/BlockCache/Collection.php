<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Model\ResourceModel\BlockCache;

use Infrangible\CacheUsage\Model\BlockCache;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection of the blocks rendered by requests.
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
        $this->_init(BlockCache::class, \Infrangible\CacheUsage\Model\ResourceModel\BlockCache::class);
    }
}
