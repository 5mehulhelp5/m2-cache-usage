<?php

namespace Infrangible\CacheUsage\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @method string getRoute()
 * @method void setRoute(string $route)s
 * @method string getController()
 * @method void setController(string $controller)
 * @method string getAction()
 * @method void setAction(string $action)
 * @method string getPath()
 * @method void setPath(string $path)
 * @method string getQuery()
 * @method void setQuery(string $query)
 * @method string getLayoutName()
 * @method void setLayoutName($layoutName)
 * @method string getClassName()
 * @method void setClassName(string $className)
 * @method string getTemplateName()
 * @method void setTemplateName(string $templateName)
 * @method int getCacheable()
 * @method void setCacheable(int $cacheable)
 * @method int getCached()
 * @method void setCached(int $cached)
 * @method int getDuration()
 * @method void setDuration(int $duration)
 */
class BlockCache
    extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\BlockCache::class);
    }
}
