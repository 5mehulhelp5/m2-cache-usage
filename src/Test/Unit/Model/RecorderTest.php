<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Test\Unit\Model;

use Infrangible\CacheUsage\Model\Cache;
use Infrangible\CacheUsage\Model\Recorder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests the rows of the cache usage: values that the columns do not allow are adjusted, so that storing never fails.
 */
class RecorderTest extends TestCase
{
    /**
     * Tests the row of the page: missing values of a request that no router matched become empty strings, and long
     * values are shortened to the column.
     *
     * @return void
     */
    public function testSaveFullPageCache(): void
    {
        $cache = new Cache();

        $cache->setQueryParameters(str_repeat('q', 3000));
        $cache->setCacheable(true);

        $connection = $this->createMock(AdapterInterface::class);
        $connection->expects($this->once())->method('insert')->with(
            'cache_usage_fpc',
            $this->callback(
                static fn(array $row): bool => $row[ 'route' ] === '' && $row[ 'path' ] === '' &&
                    mb_strlen($row[ 'query' ]) === 2000 && $row[ 'cacheable' ] === 1 && $row[ 'cached' ] === 0 &&
                    $row[ 'duration' ] === 42 && $row[ 'created_at' ] === $row[ 'updated_at' ]
            )
        );

        (new Recorder($this->createResourceConnection($connection), $cache))->saveFullPageCache(42);
    }

    /**
     * Tests that all blocks are stored with one query, with the cacheable value of their state.
     *
     * @return void
     */
    public function testSaveBlocks(): void
    {
        $layout = $this->createStub(LayoutInterface::class);
        $layout->method('getParentName')->willReturn(false);

        $cache = new Cache();

        $cache->setRouteName('cms');
        $cache->addBlockData($layout, 'menu', 'Menu', '-')->setUncacheable(true);
        $cache->addBlockData($layout, 'header', 'Header', '-')->setCached(true);
        $cache->addBlockData($layout, 'footer', 'Footer', '-')->setUncached(true);
        $cache->addBlockData($layout, 'text', 'Text', str_repeat('t', 1500));

        $connection = $this->createMock(AdapterInterface::class);
        $connection->expects($this->once())->method('insertMultiple')->with(
            'cache_usage_block',
            $this->callback(
                static fn(array $rows): bool => array_column($rows, 'cacheable') === [0, 1, 1, 2] &&
                    array_column($rows, 'cached') === [0, 1, 0, 0] &&
                    array_column($rows, 'route') === ['cms', 'cms', 'cms', 'cms'] &&
                    array_column($rows, 'duration') === [null, null, null, null] &&
                    mb_strlen($rows[ 3 ][ 'template_name' ]) === 1000
            )
        );

        (new Recorder($this->createResourceConnection($connection), $cache))->saveBlocks();
    }

    /**
     * Tests that nothing is stored without blocks.
     *
     * @return void
     */
    public function testSaveBlocksWithoutBlocks(): void
    {
        $connection = $this->createMock(AdapterInterface::class);
        $connection->expects($this->never())->method('insertMultiple');

        (new Recorder($this->createResourceConnection($connection), new Cache()))->saveBlocks();
    }

    /**
     * Returns a resource connection with the connection and table names without prefix.
     *
     * @param AdapterInterface $connection
     *
     * @return ResourceConnection
     */
    private function createResourceConnection(AdapterInterface $connection): ResourceConnection
    {
        $resourceConnection = $this->createStub(ResourceConnection::class);
        $resourceConnection->method('getConnection')->willReturn($connection);
        $resourceConnection->method('getTableName')->willReturnArgument(0);

        return $resourceConnection;
    }
}
