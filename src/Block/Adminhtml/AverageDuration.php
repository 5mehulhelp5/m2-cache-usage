<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Block\Adminhtml;

use Exception;
use Infrangible\BackendWidget\Block\Grid\GroupBy;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DB\Sql\Expression;

/**
 * Adds the average duration of the grouped rows to the grids of the cache usage.
 *
 * @mixin GroupBy
 */
trait AverageDuration
{
    /**
     * The collection selects all columns of the table.
     *
     * @param AbstractDb $collection
     *
     * @return void
     */
    protected function prepareCollection(AbstractDb $collection): void // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
    {
        // the columns are columns of the table
    }

    /**
     * Adds the average duration to the grouped rows.
     *
     * @param AbstractDb $collection
     *
     * @return void
     */
    protected function followUpCollection(AbstractDb $collection): void
    {
        parent::followUpCollection($collection);

        if (count($this->getGroupByFieldNames()) > 0) {
            $collection->getSelect()->columns([new Expression('ROUND(AVG(duration), 0) AS average_duration')]);
        }
    }

    /**
     * Adds the column of the average duration to the grouped rows.
     *
     * @return void
     * @throws Exception
     */
    protected function followUpFields(): void
    {
        parent::followUpFields();

        if (count($this->getGroupByFieldNames()) > 0) {
            $this->addColumn(
                'average_duration',
                [
                    'header'           => __('Average Duration'),
                    'index'            => 'average_duration',
                    'type'             => 'number',
                    'column_css_class' => 'data-grid-td',
                    'filter'           => false
                ]
            );
        }
    }

    /**
     * Returns the fields that are not grouped: the duration and the date of the single rows.
     *
     * @return string[]
     */
    public function getNotGroupableFieldNames(): array
    {
        return ['duration', 'created_at', 'average_duration'];
    }
}
