<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Block\Adminhtml\FullPageCache;

use Exception;
use Infrangible\BackendWidget\Block\Grid\GroupBy;
use Magento\Framework\Data\Collection\AbstractDb;
use Zend_Db_Expr;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Grid
    extends GroupBy
{
    /**
     * @param AbstractDb $collection
     *
     * @return void
     */
    protected function prepareCollection(AbstractDb $collection)
    {
    }

    /**
     * @param AbstractDb $collection
     */
    protected function followUpCollection(AbstractDb $collection)
    {
        parent::followUpCollection($collection);

        $groupBy = $this->getParam('group_by');

        if (!$this->variables->isEmpty($groupBy)) {
            $select = $collection->getSelect();

            $select->columns([new Zend_Db_Expr('ROUND(AVG(duration), 0) AS average_duration')]);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function prepareFields()
    {
        $this->addTextColumn('route', __('Route')->render());
        $this->addTextColumn('controller', __('Controller')->render());
        $this->addTextColumn('action', __('Action')->render());
        $this->addTextColumn('path', __('Path')->render());
        $this->addTextColumn('query', __('Query')->render());
        $this->addYesNoColumn('cacheable', __('Cacheable')->render());
        $this->addYesNoColumn('cached', __('Cached')->render());
        $this->addNumberColumn('duration', __('Duration')->render());
        $this->addDatetimeColumn('created_at', __('Date')->render());
    }

    /**
     * @return string[]
     */
    protected function getHiddenFieldNames(): array
    {
        return ['query'];
    }

    /**
     * @return array
     */
    public function getNotGroupableFieldNames(): array
    {
        return ['duration', 'created_at', 'average_duration'];
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function followUpFields()
    {
        parent::followUpFields();

        $groupBy = $this->getParam('group_by');

        if (!$this->variables->isEmpty($groupBy)) {
            $this->addColumn('average_duration', [
                'header'           => __('Average'),
                'index'            => 'average_duration',
                'type'             => 'number',
                'column_css_class' => 'data-grid-td',
                'filter'           => false
            ]);
        }
    }
}
