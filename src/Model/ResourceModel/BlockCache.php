<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource model of the blocks rendered by requests.
 *
 * The rows are written by the recorder, here they are only read and deleted.
 */
class BlockCache extends AbstractDb
{
    /** Name of the table. */
    public const TABLE_NAME = 'cache_usage_block';

    /**
     * Initializes the model and the resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, 'id');
    }
}
