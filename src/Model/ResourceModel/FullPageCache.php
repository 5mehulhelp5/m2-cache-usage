<?php

namespace Infrangible\CacheUsage\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Zend_Date;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class FullPageCache
    extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cache_usage_fpc', 'id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object): AbstractDb
    {
        parent::_beforeSave($object);

        /** @var \Infrangible\CacheUsage\Model\FullPageCache $object */
        if ($object->isObjectNew()) {
            $object->setCreatedAt(Zend_Date::now());
        }

        $object->setUpdatedAt(Zend_Date::now());

        return $this;
    }
}
