<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Task;

use FeWeDev\Base\Variables;
use Infrangible\CacheUsage\Model\ResourceModel\BlockCache;
use Infrangible\CacheUsage\Model\ResourceModel\FullPageCache;
use Infrangible\Core\Helper\Database;
use Infrangible\Core\Helper\Registry;
use Infrangible\SimpleMail\Model\MailFactory;
use Infrangible\Task\Helper\Data;
use Infrangible\Task\Model\ResourceModel\RunFactory as RunResourceFactory;
use Infrangible\Task\Model\RunFactory;
use Infrangible\Task\Task\Base;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Filesystem\Driver\File;
use Psr\Log\LoggerInterface;

/**
 * Removes all collected cache usage of pages and blocks.
 */
class Clear extends Base
{
    /** @var bool */
    private bool $emptyRun = true;

    /**
     * @param File $file
     * @param Registry $registryHelper
     * @param Data $helper
     * @param LoggerInterface $logging
     * @param DirectoryList $directoryList
     * @param RunFactory $runFactory
     * @param RunResourceFactory $runResourceFactory
     * @param MailFactory $mailFactory
     * @param Database $databaseHelper
     * @param Variables $variables
     */
    public function __construct(
        File $file,
        Registry $registryHelper,
        Data $helper,
        LoggerInterface $logging,
        DirectoryList $directoryList,
        RunFactory $runFactory,
        RunResourceFactory $runResourceFactory,
        MailFactory $mailFactory,
        protected Database $databaseHelper,
        protected Variables $variables
    ) {
        parent::__construct(
            $file,
            $registryHelper,
            $helper,
            $logging,
            $directoryList,
            $runFactory,
            $runResourceFactory,
            $mailFactory
        );
    }

    /**
     * The task needs no preparation.
     *
     * @return void
     */
    protected function prepare(): void // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
    {
        // the rows are removed with one query per table
    }

    /**
     * Removes the rows of the pages and blocks.
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    protected function runTask(): bool
    {
        $fullPageCacheCount = $this->removeRows(FullPageCache::TABLE_NAME);

        if ($fullPageCacheCount > 0) {
            $this->logging->info(__('Removing %1 full page cache entries.', $fullPageCacheCount)->render());
        }

        $blockCacheCount = $this->removeRows(BlockCache::TABLE_NAME);

        if ($blockCacheCount > 0) {
            $this->logging->info(__('Removing %1 block cache entries.', $blockCacheCount)->render());
        }

        if ($this->isEmptyRun()) {
            $this->logging->info(__('No cache entries to remove.')->render());
        }

        return true;
    }

    /**
     * Removes the rows of the table that match the condition of the task, and returns their number.
     *
     * @param string $tableName
     *
     * @return int
     * @throws CouldNotDeleteException
     */
    protected function removeRows(string $tableName): int
    {
        $dbAdapter = $this->databaseHelper->getDefaultConnection();
        $table = $this->databaseHelper->getTableName($tableName);
        $condition = $this->getCondition();

        $select = $dbAdapter->select()->from($table, ['COUNT(*)']);

        if ($condition !== null) {
            $select->where($condition);
        }

        $count = $this->variables->intValue($this->databaseHelper->fetchOne($select) ?? 0);

        if ($count > 0) {
            $this->databaseHelper->deleteTableData($dbAdapter, $table, $condition, $this->isTest());

            $this->emptyRun = false;
        }

        return $count;
    }

    /**
     * Returns the condition of the rows to remove, null for all rows.
     *
     * @return string|null
     */
    protected function getCondition(): ?string
    {
        return null;
    }

    /**
     * Checks if the run removed nothing.
     *
     * @return bool
     */
    public function isEmptyRun(): bool
    {
        return $this->emptyRun;
    }

    /**
     * The task needs no clean up.
     *
     * @param bool $success
     *
     * @return void
     */
    protected function dismantle(bool $success): void // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
    {
        // nothing was prepared
    }
}
