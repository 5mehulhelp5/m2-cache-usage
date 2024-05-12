<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallSchema
    implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->createFullPageCacheTable($setup->getConnection());
        $this->createBlockCacheTable($setup->getConnection());
    }

    /**
     * @param AdapterInterface $dbAdapter
     *
     * @throws Zend_Db_Exception
     */
    protected function createFullPageCacheTable(AdapterInterface $dbAdapter)
    {
        $tableName = $dbAdapter->getTableName('cache_usage_fpc');

        if ($dbAdapter->isTableExists($tableName)) {
            $dbAdapter->dropTable($tableName);
        }

        $table = $dbAdapter->newTable($tableName);

        $table->addColumn('id', Table::TYPE_SMALLINT, 6,
            ['identity' => true, 'primary' => true, 'nullable' => false, 'unsigned' => true]);
        $table->addColumn('route', Table::TYPE_TEXT, 255, ['nullable' => false]);
        $table->addColumn('controller', Table::TYPE_TEXT, 255, ['nullable' => false]);
        $table->addColumn('action', Table::TYPE_TEXT, 255, ['nullable' => false]);
        $table->addColumn('path', Table::TYPE_TEXT, 255, ['nullable' => false]);
        $table->addColumn('query', Table::TYPE_TEXT, 2000, ['nullable' => false]);
        $table->addColumn('cacheable', Table::TYPE_SMALLINT, 1,
            ['nullable' => false, 'unsigned' => true, 'default' => 0]);
        $table->addColumn('cached', Table::TYPE_SMALLINT, 1, ['nullable' => false, 'unsigned' => true, 'default' => 0]);
        $table->addColumn('duration', Table::TYPE_INTEGER, 12,
            ['nullable' => true, 'unsigned' => true, 'default' => 0]);
        $table->addColumn('created_at', Table::TYPE_DATETIME, null, ['nullable' => false]);
        $table->addColumn('updated_at', Table::TYPE_DATETIME, null, ['nullable' => false]);

        $table->addIndex($dbAdapter->getIndexName($tableName, ['route']), ['route']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'cacheable']), ['route', 'cacheable']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'cacheable', 'cached']),
            ['route', 'cacheable', 'cached']);

        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller']), ['route', 'controller']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'cacheable']),
            ['route', 'controller', 'cacheable']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'cacheable', 'cached']),
            ['route', 'controller', 'cacheable', 'cached']);

        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'action']),
            ['route', 'controller', 'action']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'action', 'cacheable']),
            ['route', 'controller', 'action', 'cacheable']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'action', 'cacheable', 'cached']),
            ['route', 'controller', 'action', 'cacheable', 'cached']);

        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'action', 'path']),
            ['route', 'controller', 'action', 'path']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'action', 'path', 'cacheable']),
            ['route', 'controller', 'action', 'path', 'cacheable']);
        $table->addIndex($dbAdapter->getIndexName($tableName,
            ['route', 'controller', 'action', 'path', 'cacheable', 'cached']),
            ['route', 'controller', 'action', 'path', 'cacheable', 'cached']);

        $table->addIndex($dbAdapter->getIndexName($tableName, ['cacheable']), ['cacheable']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['cached']), ['cached']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['cacheable', 'cached']), ['cacheable', 'cached']);

        $dbAdapter->createTable($table);
    }

    /**
     * @param AdapterInterface $dbAdapter
     *
     * @throws Zend_Db_Exception
     */
    protected function createBlockCacheTable(AdapterInterface $dbAdapter)
    {
        $tableName = $dbAdapter->getTableName('cache_usage_block');

        if ($dbAdapter->isTableExists($tableName)) {
            $dbAdapter->dropTable($tableName);
        }

        $table = $dbAdapter->newTable($tableName);

        $table->addColumn('id', Table::TYPE_SMALLINT, 6,
            ['identity' => true, 'primary' => true, 'nullable' => false, 'unsigned' => true]);
        $table->addColumn('route', Table::TYPE_TEXT, 255, ['nullable' => false]);
        $table->addColumn('controller', Table::TYPE_TEXT, 255, ['nullable' => false]);
        $table->addColumn('action', Table::TYPE_TEXT, 255, ['nullable' => false]);
        $table->addColumn('path', Table::TYPE_TEXT, 255, ['nullable' => false]);
        $table->addColumn('query', Table::TYPE_TEXT, 2000, ['nullable' => false]);
        $table->addColumn('layout_name', Table::TYPE_TEXT, 255, ['nullable' => false]);
        $table->addColumn('class_name', Table::TYPE_TEXT, 255, ['nullable' => false]);
        $table->addColumn('template_name', Table::TYPE_TEXT, 255, ['nullable' => false]);
        $table->addColumn('cacheable', Table::TYPE_SMALLINT, 1,
            ['nullable' => false, 'unsigned' => true, 'default' => 2]);
        $table->addColumn('cached', Table::TYPE_SMALLINT, 1, ['nullable' => false, 'unsigned' => true, 'default' => 0]);
        $table->addColumn('duration', Table::TYPE_INTEGER, 12,
            ['nullable' => true, 'unsigned' => true, 'default' => 0]);
        $table->addColumn('created_at', Table::TYPE_DATETIME, null, ['nullable' => false]);
        $table->addColumn('updated_at', Table::TYPE_DATETIME, null, ['nullable' => false]);

        $table->addIndex($dbAdapter->getIndexName($tableName, ['route']), ['route']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'cacheable']), ['route', 'cacheable']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'cacheable', 'cached']),
            ['route', 'cacheable', 'cached']);

        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller']), ['route', 'controller']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'cacheable']),
            ['route', 'controller', 'cacheable']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'cacheable', 'cached']),
            ['route', 'controller', 'cacheable', 'cached']);

        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'action']),
            ['route', 'controller', 'action']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'action', 'cacheable']),
            ['route', 'controller', 'action', 'cacheable']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'action', 'cacheable', 'cached']),
            ['route', 'controller', 'action', 'cacheable', 'cached']);

        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'action', 'path']),
            ['route', 'controller', 'action', 'path']);
        $table->addIndex($dbAdapter->getIndexName($tableName, ['route', 'controller', 'action', 'path', 'cacheable']),
            ['route', 'controller', 'action', 'path', 'cacheable']);
        $table->addIndex($dbAdapter->getIndexName($tableName,
            ['route', 'controller', 'action', 'path', 'cacheable', 'cached']),
            ['route', 'controller', 'action', 'path', 'cacheable', 'cached']);

        $dbAdapter->createTable($table);
    }
}
