<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Task;

use Exception;
use FeWeDev\Base\Files;
use Infrangible\CacheUsage\Model\ResourceModel\FullPageCache\Collection;
use Infrangible\CacheUsage\Model\ResourceModel\FullPageCache\CollectionFactory;
use Infrangible\Core\Helper\Database;
use Infrangible\Core\Helper\Registry;
use Infrangible\Task\Helper\Data;
use Infrangible\Task\Model\RunFactory;
use Infrangible\Task\Task\Base;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Clear
    extends Base
{
    /** @var Database */
    protected $databaseHelper;

    /** @var CollectionFactory */
    protected $fullPageCacheCollectionFactory;

    /** @var \Infrangible\CacheUsage\Model\ResourceModel\BlockCache\CollectionFactory */
    protected $blockCacheCollectionFactory;

    /** @var bool */
    private $emptyRun = true;

    /**
     * @param Files                                                                    $files
     * @param Registry                                                                 $registryHelper
     * @param Data                                                                     $helper
     * @param Database                                                                 $databaseHelper
     * @param LoggerInterface                                                          $logging
     * @param Emulation                                                                $appEmulation
     * @param DirectoryList                                                            $directoryList
     * @param TransportBuilder                                                         $transportBuilder
     * @param RunFactory                                                               $runFactory
     * @param \Infrangible\Task\Model\ResourceModel\RunFactory                         $runResourceFactory
     * @param CollectionFactory                                                        $fullPageCacheCollectionFactory
     * @param \Infrangible\CacheUsage\Model\ResourceModel\BlockCache\CollectionFactory $blockCacheCollectionFactory
     */
    public function __construct(
        Files $files,
        Registry $registryHelper,
        Data $helper,
        Database $databaseHelper,
        LoggerInterface $logging,
        Emulation $appEmulation,
        DirectoryList $directoryList,
        TransportBuilder $transportBuilder,
        RunFactory $runFactory,
        \Infrangible\Task\Model\ResourceModel\RunFactory $runResourceFactory,
        CollectionFactory $fullPageCacheCollectionFactory,
        \Infrangible\CacheUsage\Model\ResourceModel\BlockCache\CollectionFactory $blockCacheCollectionFactory
    ) {
        parent::__construct(
            $files,
            $registryHelper,
            $helper,
            $logging,
            $appEmulation,
            $directoryList,
            $transportBuilder,
            $runFactory,
            $runResourceFactory
        );

        $this->databaseHelper = $databaseHelper;

        $this->fullPageCacheCollectionFactory = $fullPageCacheCollectionFactory;
        $this->blockCacheCollectionFactory = $blockCacheCollectionFactory;
    }

    /**
     * @return void
     */
    protected function prepare()
    {
    }

    /**
     * @throws Exception
     */
    protected function runTask(): void
    {
        $dbAdapter = $this->databaseHelper->getDefaultConnection();

        $fullPageCacheCollection = $this->fullPageCacheCollectionFactory->create();

        $this->prepareFullPageCacheCollection($fullPageCacheCollection);

        $fullPageCacheIds = $fullPageCacheCollection->getAllIds();

        if (count($fullPageCacheIds) > 0) {
            $this->logging->info(__('Removing %1 full page cache entries.', count($fullPageCacheIds)));

            foreach (array_chunk($fullPageCacheIds, 100) as $fullPageCacheIdsChunk) {
                $this->databaseHelper->deleteTableData(
                    $dbAdapter,
                    'cache_usage_fpc',
                    sprintf('id IN (%s)', implode(',', $fullPageCacheIdsChunk))
                );
            }

            $this->emptyRun = false;
        }

        $blockCacheCollection = $this->blockCacheCollectionFactory->create();

        $this->prepareBlockCacheCollection($blockCacheCollection);

        $blockCacheIds = $blockCacheCollection->getAllIds();

        if (count($blockCacheIds) > 0) {
            $this->logging->info(__('Removing %1 block page cache entries.', count($blockCacheIds)));

            foreach (array_chunk($blockCacheIds, 100) as $blockCacheIdsChunk) {
                $this->databaseHelper->deleteTableData(
                    $dbAdapter,
                    'cache_usage_block',
                    sprintf('id IN (%s)', implode(',', $blockCacheIdsChunk))
                );
            }

            $this->emptyRun = false;
        }

        if ($this->isEmptyRun()) {
            $this->logging->info(__('No cache entries to remove.'));
        }
    }

    /**
     * @param Collection $fullPageCacheCollection
     *
     * @return void
     */
    protected function prepareFullPageCacheCollection(Collection $fullPageCacheCollection): void
    {
    }

    /**
     * @param \Infrangible\CacheUsage\Model\ResourceModel\BlockCache\Collection $blockCacheCollection
     *
     * @return void
     */
    protected function prepareBlockCacheCollection(
        \Infrangible\CacheUsage\Model\ResourceModel\BlockCache\Collection $blockCacheCollection
    ): void {
    }

    /**
     * @return bool
     */
    public function isEmptyRun(): bool
    {
        return $this->emptyRun;
    }

    /**
     * @param bool $success
     *
     * @return void
     */
    protected function dismantle(bool $success)
    {
    }
}