<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * A full page cache usage of a request, as row of the grid. The values are strings, as read from the database.
 *
 * @method string getRoute()
 * @method string getController()
 * @method string getAction()
 * @method string getPath()
 * @method string getQuery()
 * @method string getCacheable()
 * @method string getCached()
 * @method string|null getDuration()
 * @method string getCreatedAt()
 */
class FullPageCache extends AbstractModel
{
    /**
     * Initializes the model and the resource model.
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel\FullPageCache::class);
    }
}
