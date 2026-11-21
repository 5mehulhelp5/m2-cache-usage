<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Model;

use Infrangible\CacheUsage\Model\Cache\Block;
use Infrangible\CacheUsage\Model\ResourceModel\BlockCache;
use Infrangible\CacheUsage\Model\ResourceModel\FullPageCache;
use Magento\Framework\App\ResourceConnection;

/**
 * Stores the cache usage of the request: one row for the page and one row per rendered block.
 */
class Recorder
{
    /** Value of the column "cacheable" of blocks without own cache, which are only cached with the page. */
    public const CACHEABLE_UNDEFINED = 2;

    /** @var array<string, int> maximal lengths of the text columns */
    private const COLUMN_LENGTHS = [
        'route'         => 255,
        'controller'    => 255,
        'action'        => 255,
        'path'          => 255,
        'query'         => 2000,
        'layout_name'   => 1000,
        'class_name'    => 255,
        'template_name' => 1000
    ];

    /**
     * @param ResourceConnection $resourceConnection
     * @param Cache $cache
     */
    public function __construct(protected ResourceConnection $resourceConnection, protected Cache $cache)
    {
    }

    /**
     * Stores the usage of the full page cache by the request.
     *
     * @param int|null $duration the duration of the request in milliseconds
     *
     * @return void
     */
    public function saveFullPageCache(?int $duration): void
    {
        $connection = $this->resourceConnection->getConnection();

        $connection->insert(
            $this->resourceConnection->getTableName(FullPageCache::TABLE_NAME),
            array_merge(
                $this->getRequestData(),
                [
                    'cacheable' => (int)$this->cache->isCacheable(),
                    'cached'    => (int)$this->cache->isCached(),
                    'duration'  => $duration
                ],
                $this->getTimestamps()
            )
        );
    }

    /**
     * Stores the blocks rendered by the request.
     *
     * @return void
     */
    public function saveBlocks(): void
    {
        $blocks = $this->cache->getBlocks();

        if (count($blocks) === 0) {
            return;
        }

        $requestData = $this->getRequestData();
        $timestamps = $this->getTimestamps();

        $rows = array_map(
            fn(Block $block): array => array_merge(
                $requestData,
                [
                    'layout_name'   => $this->truncate('layout_name', $block->getLayoutName()),
                    'class_name'    => $this->truncate('class_name', $block->getClassName()),
                    'template_name' => $this->truncate('template_name', $block->getTemplateName()),
                    'cacheable'     => $this->getBlockCacheable($block),
                    'cached'        => (int)$block->isCached(),
                    'duration'      => $block->getDuration()
                ],
                $timestamps
            ),
            array_values($blocks)
        );

        $this->resourceConnection->getConnection()->insertMultiple(
            $this->resourceConnection->getTableName(BlockCache::TABLE_NAME),
            $rows
        );
    }

    /**
     * Returns the value of the column "cacheable" of the block.
     *
     * 0 for not cacheable, 1 for cacheable and 2 for blocks without own cache.
     *
     * @param Block $block
     *
     * @return int
     */
    protected function getBlockCacheable(Block $block): int
    {
        return match ($block->getState()) {
            Block::STATE_UNCACHEABLE => 0,
            Block::STATE_CACHED, Block::STATE_UNCACHED => 1,
            default => self::CACHEABLE_UNDEFINED
        };
    }

    /**
     * Returns the route, controller, action and parameters of the request.
     *
     * @return array<string, string>
     */
    protected function getRequestData(): array
    {
        return [
            'route'      => $this->truncate('route', $this->cache->getRouteName()),
            'controller' => $this->truncate('controller', $this->cache->getControllerName()),
            'action'     => $this->truncate('action', $this->cache->getActionName()),
            'path'       => $this->truncate('path', $this->cache->getPathParameters()),
            'query'      => $this->truncate('query', $this->cache->getQueryParameters())
        ];
    }

    /**
     * Returns the creation and update time in GMT.
     *
     * @return array<string, string>
     */
    protected function getTimestamps(): array
    {
        $now = gmdate('Y-m-d H:i:s');

        return ['created_at' => $now, 'updated_at' => $now];
    }

    /**
     * Returns the value shortened to the length of the column, an empty string for null.
     *
     * @param string $column
     * @param string|null $value
     *
     * @return string
     */
    protected function truncate(string $column, ?string $value): string
    {
        return mb_substr($value ?? '', 0, self::COLUMN_LENGTHS[ $column ]);
    }
}
