<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Task;

use Exception;
use FeWeDev\Base\Files;
use Infrangible\CacheUsage\Model\ResourceModel\FullPageCache\Collection;
use Infrangible\CacheUsage\Model\ResourceModel\FullPageCache\CollectionFactory;
use Infrangible\Core\Helper\Database;
use Infrangible\Core\Helper\Registry;
use Infrangible\SimpleMail\Model\MailFactory;
use Infrangible\Task\Helper\Data;
use Infrangible\Task\Model\RunFactory;
use Infrangible\Task\Task\Base;
use Magento\Framework\App\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Clear extends Base
{
    /** @var Database */
    protected $databaseHelper;

    /** @var CollectionFactory */
    protected $fullPageCacheCollectionFactory;

    /** @var \Infrangible\CacheUsage\Model\ResourceModel\BlockCache\CollectionFactory */
    protected $blockCacheCollectionFactory;

    /** @var bool */
    private $emptyRun = true;

    public function __construct(
        Files $files,
        Registry $registryHelper,
        Data $helper,
        LoggerInterface $logging,
        DirectoryList $directoryList,
        RunFactory $runFactory,
        \Infrangible\Task\Model\ResourceModel\RunFactory $runResourceFactory,
        MailFactory $mailFactory,
        Database $databaseHelper,
        CollectionFactory $fullPageCacheCollectionFactory,
        \Infrangible\CacheUsage\Model\ResourceModel\BlockCache\CollectionFactory $blockCacheCollectionFactory
    ) {
        parent::__construct(
            $files,
            $registryHelper,
            $helper,
            $logging,
            $directoryList,
            $runFactory,
            $runResourceFactory,
            $mailFactory
        );

        $this->databaseHelper = $databaseHelper;
        $this->fullPageCacheCollectionFactory = $fullPageCacheCollectionFactory;
        $this->blockCacheCollectionFactory = $blockCacheCollectionFactory;
    }

    protected function prepare(): void
    {
    }

    /**
     * @throws Exception
     */
    protected function runTask(): bool
    {
        $dbAdapter = $this->databaseHelper->getDefaultConnection();

        $fullPageCacheCollection = $this->fullPageCacheCollectionFactory->create();

        $this->prepareFullPageCacheCollection($fullPageCacheCollection);

        $fullPageCacheIds = $fullPageCacheCollection->getAllIds();

        if (count($fullPageCacheIds) > 0) {
            $this->logging->info(
                __(
                    'Removing %1 full page cache entries.',
                    count($fullPageCacheIds)
                )
            );

            foreach (array_chunk(
                $fullPageCacheIds,
                100
            ) as $fullPageCacheIdsChunk) {
                $this->databaseHelper->deleteTableData(
                    $dbAdapter,
                    'cache_usage_fpc',
                    sprintf(
                        'id IN (%s)',
                        implode(
                            ',',
                            $fullPageCacheIdsChunk
                        )
                    )
                );
            }

            $this->emptyRun = false;
        }

        $blockCacheCollection = $this->blockCacheCollectionFactory->create();

        $this->prepareBlockCacheCollection($blockCacheCollection);

        $blockCacheIds = $blockCacheCollection->getAllIds();

        if (count($blockCacheIds) > 0) {
            $this->logging->info(
                __(
                    'Removing %1 block page cache entries.',
                    count($blockCacheIds)
                )
            );

            foreach (array_chunk(
                $blockCacheIds,
                100
            ) as $blockCacheIdsChunk) {
                $this->databaseHelper->deleteTableData(
                    $dbAdapter,
                    'cache_usage_block',
                    sprintf(
                        'id IN (%s)',
                        implode(
                            ',',
                            $blockCacheIdsChunk
                        )
                    )
                );
            }

            $this->emptyRun = false;
        }

        if ($this->isEmptyRun()) {
            $this->logging->info(__('No cache entries to remove.'));
        }

        return true;
    }

    protected function prepareFullPageCacheCollection(Collection $fullPageCacheCollection): void
    {
    }

    protected function prepareBlockCacheCollection(
        \Infrangible\CacheUsage\Model\ResourceModel\BlockCache\Collection $blockCacheCollection
    ): void {
    }

    public function isEmptyRun(): bool
    {
        return $this->emptyRun;
    }

    protected function dismantle(bool $success): void
    {
    }
}
