<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Task;

use Infrangible\CacheUsage\Model\ResourceModel\FullPageCache\Collection;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Reduce
    extends Clear
{
    /**
     * @param Collection $fullPageCacheCollection
     *
     * @return void
     */
    protected function prepareFullPageCacheCollection(Collection $fullPageCacheCollection): void
    {
        parent::prepareFullPageCacheCollection($fullPageCacheCollection);

        $fullPageCacheCollection->addCreatedAtLimit(30);
    }

    /**
     * @param \Infrangible\CacheUsage\Model\ResourceModel\BlockCache\Collection $blockCacheCollection
     *
     * @return void
     */
    protected function prepareBlockCacheCollection(
        \Infrangible\CacheUsage\Model\ResourceModel\BlockCache\Collection $blockCacheCollection): void
    {
        parent::prepareBlockCacheCollection($blockCacheCollection);

        $blockCacheCollection->addCreatedAtLimit(30);
    }
}
