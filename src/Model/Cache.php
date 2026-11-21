<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Model;

use Infrangible\CacheUsage\Model\Cache\Block;
use Infrangible\CacheUsage\Model\Cache\Timing;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

/**
 * The cache usage of the current request: the matched route, whether the page is cacheable and was cached, and the
 * rendered blocks. Shared by the plugins and observers of the request.
 */
class Cache
{
    use Timing;

    /** @var string|null */
    private ?string $routeName = null;

    /** @var string|null */
    private ?string $controllerName = null;

    /** @var string|null */
    private ?string $actionName = null;

    /** @var string|null */
    private ?string $pathParameters = null;

    /** @var string|null */
    private ?string $queryParameters = null;

    /** @var bool */
    private bool $cacheable = false;

    /** @var bool */
    private bool $cached = false;

    /** @var array<string, Block> blocks by name in the layout */
    private array $blocks = [];

    /** @var string|null the running operation of the page cache kernel, "load" or "process" */
    private ?string $pageCacheOperation = null;

    /**
     * Returns the page cache operation.
     *
     * @return string|null
     */
    public function getPageCacheOperation(): ?string
    {
        return $this->pageCacheOperation;
    }

    /**
     * Sets the page cache operation.
     *
     * @param string|null $pageCacheOperation
     *
     * @return void
     */
    public function setPageCacheOperation(?string $pageCacheOperation): void
    {
        $this->pageCacheOperation = $pageCacheOperation;
    }

    /**
     * Returns the data of the request that the full page cache stores with the page.
     *
     * @return array<string, mixed>
     */
    public function getPageCacheData(): array
    {
        return [
            'route'            => $this->getRouteName(),
            'controller'       => $this->getControllerName(),
            'action'           => $this->getActionName(),
            'path_parameters'  => $this->getPathParameters(),
            'query_parameters' => $this->getQueryParameters(),
            'blocks'           => array_map(
                static fn(Block $block): array => $block->jsonSerialize(),
                $this->getBlocks()
            )
        ];
    }

    /**
     * Sets the data of the request that the full page cache stored with the page.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function setPageCacheData(mixed $data): void
    {
        if (! is_array($data)) {
            return;
        }

        $this->setRouteName($this->getStringValue($data, 'route'));
        $this->setControllerName($this->getStringValue($data, 'controller'));
        $this->setActionName($this->getStringValue($data, 'action'));
        $this->setPathParameters($this->getStringValue($data, 'path_parameters'));
        $this->setQueryParameters($this->getStringValue($data, 'query_parameters'));
        $this->setBlocksData($data[ 'blocks' ] ?? null);
    }

    /**
     * Returns the route name.
     *
     * @return string|null
     */
    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    /**
     * Sets the route name.
     *
     * @param string|null $routeName
     *
     * @return void
     */
    public function setRouteName(?string $routeName): void
    {
        $this->routeName = $routeName;
    }

    /**
     * Returns the controller name.
     *
     * @return string|null
     */
    public function getControllerName(): ?string
    {
        return $this->controllerName;
    }

    /**
     * Sets the controller name.
     *
     * @param string|null $controllerName
     *
     * @return void
     */
    public function setControllerName(?string $controllerName): void
    {
        $this->controllerName = $controllerName;
    }

    /**
     * Returns the action name.
     *
     * @return string|null
     */
    public function getActionName(): ?string
    {
        return $this->actionName;
    }

    /**
     * Sets the action name.
     *
     * @param string|null $actionName
     *
     * @return void
     */
    public function setActionName(?string $actionName): void
    {
        $this->actionName = $actionName;
    }

    /**
     * Returns the path parameters.
     *
     * @return string|null
     */
    public function getPathParameters(): ?string
    {
        return $this->pathParameters;
    }

    /**
     * Sets the path parameters.
     *
     * @param string|null $pathParameters
     *
     * @return void
     */
    public function setPathParameters(?string $pathParameters): void
    {
        $this->pathParameters = $pathParameters;
    }

    /**
     * Returns the query parameters.
     *
     * @return string|null
     */
    public function getQueryParameters(): ?string
    {
        return $this->queryParameters;
    }

    /**
     * Sets the query parameters.
     *
     * @param string|null $queryParameters
     *
     * @return void
     */
    public function setQueryParameters(?string $queryParameters): void
    {
        $this->queryParameters = $queryParameters;
    }

    /**
     * Checks if the request is cacheable.
     *
     * @return bool
     */
    public function isCacheable(): bool
    {
        return $this->cacheable;
    }

    /**
     * Sets the cacheable.
     *
     * @param bool $cacheable
     *
     * @return void
     */
    public function setCacheable(bool $cacheable): void
    {
        $this->cacheable = $cacheable;
    }

    /**
     * Checks if the request is cached.
     *
     * @return bool
     */
    public function isCached(): bool
    {
        return $this->cached;
    }

    /**
     * Sets the cached.
     *
     * @param bool $cached
     *
     * @return void
     */
    public function setCached(bool $cached): void
    {
        $this->cached = $cached;
    }

    /**
     * Returns the blocks.
     *
     * @return array<string, Block>
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * Sets the blocks from the data that the full page cache stored with the page.
     *
     * @param mixed $blocksData
     *
     * @return void
     */
    public function setBlocksData(mixed $blocksData): void
    {
        if (! is_array($blocksData)) {
            return;
        }

        foreach ($blocksData as $blockName => $blockData) {
            if (! is_array($blockData)) {
                continue;
            }

            $block = new Block();

            $block->setLayoutName($this->getStringValue($blockData, 'layout_name'));
            $block->setClassName($this->getStringValue($blockData, 'class_name'));
            $block->setTemplateName($this->getStringValue($blockData, 'template_name'));
            $block->setUncacheable(($blockData[ 'uncacheable' ] ?? false) === true);
            $block->setCached(($blockData[ 'cached' ] ?? false) === true);
            $block->setUncached(($blockData[ 'uncached' ] ?? false) === true);

            $this->blocks[ (string)$blockName ] = $block;
        }
    }

    /**
     * Returns the value of the key if it is a string, else null.
     *
     * @param array $data
     * @phpstan-param array<int|string, mixed> $data
     * @param string $key
     *
     * @return string|null
     */
    private function getStringValue(array $data, string $key): ?string
    {
        return isset($data[ $key ]) && is_string($data[ $key ]) ? $data[ $key ] : null;
    }

    /**
     * Returns the blocks with the cache state, sorted by their path in the layout.
     *
     * @param string $state one of the STATE constants of the block
     *
     * @return array<string, Block> blocks by path in the layout
     */
    public function getBlocksByState(string $state): array
    {
        $blocks = [];

        foreach ($this->getBlocks() as $blockName => $block) {
            if ($block->getState() === $state) {
                $blocks[ $block->getLayoutName() ?? $blockName ] = $block;
            }
        }

        ksort($blocks);

        return $blocks;
    }

    /**
     * Adds the block or updates its names, and returns it.
     *
     * @param LayoutInterface|null $layout
     * @param string $name
     * @param string $className
     * @param string $templateName
     *
     * @return Block
     */
    public function addBlockData(
        ?LayoutInterface $layout,
        string $name,
        string $className,
        string $templateName
    ): Block {
        $cacheBlock = $this->getBlock($name) ?? new Block();

        $cacheBlock->setLayoutName($this->getLayoutName($layout, $name));
        $cacheBlock->setClassName($className);
        $cacheBlock->setTemplateName($templateName);

        $this->blocks[ $name ] = $cacheBlock;

        return $cacheBlock;
    }

    /**
     * Adds the block or updates its names, and returns it.
     *
     * @param AbstractBlock $block
     *
     * @return Block
     */
    public function addBlock(AbstractBlock $block): Block
    {
        try {
            $layout = $block->getLayout();
        } catch (LocalizedException) {
            // a block without layout has no parents
            $layout = null;
        }

        return $this->addBlockData(
            $layout,
            $this->getBlockName($block),
            $block::class,
            $this->getTemplateName($block)
        );
    }

    /**
     * Adds the block, before it is rendered, and starts measuring its rendering.
     *
     * @param AbstractBlock $block
     *
     * @return Block
     */
    public function startBlock(AbstractBlock $block): Block
    {
        $cacheBlock = $this->addBlock($block);

        $cacheBlock->setStarted(microtime(true));
        $cacheBlock->setFinished(null);

        return $cacheBlock;
    }

    /**
     * Returns the template file of the block, or "-" for blocks without template.
     *
     * @param AbstractBlock $block
     *
     * @return string
     */
    public function getTemplateName(AbstractBlock $block): string
    {
        if (! $block instanceof Template || $block->getTemplate() === '') {
            return '-';
        }

        $templateFile = $block->getTemplateFile();

        return is_string($templateFile) && $templateFile !== '' ? $templateFile : '-';
    }

    /**
     * Returns the name of the block in the layout, or a name from its data for blocks without name.
     *
     * @param AbstractBlock $block
     *
     * @return string
     */
    public function getBlockName(AbstractBlock $block): string
    {
        $blockName = $block->getNameInLayout();

        return is_string($blockName) && $blockName !== '' ? $blockName : $this->generateBlockName($block);
    }

    /**
     * Returns a name for a block without name in the layout, from its class and its scalar data.
     *
     * @param AbstractBlock $block
     *
     * @return string
     */
    public function generateBlockName(AbstractBlock $block): string
    {
        $blockData = $block->getData();
        $data = is_array($blockData) ? array_filter($blockData, 'is_scalar') : [];

        ksort($data);

        return 'anonymous/' . hash('sha256', $block::class . (json_encode($data) ?: ''));
    }

    /**
     * Returns the block with the name in the layout, or null.
     *
     * @param string $blockName
     *
     * @return Block|null
     */
    public function getBlock(string $blockName): ?Block
    {
        return $this->blocks[ $blockName ] ?? null;
    }

    /**
     * Returns the path of the block in the layout: the names of its parents and its name, separated by "/".
     *
     * @param LayoutInterface|null $layout
     * @param string $nameInLayout
     *
     * @return string
     */
    protected function getLayoutName(?LayoutInterface $layout, string $nameInLayout): string
    {
        $names = [$nameInLayout];
        $parentName = $layout?->getParentName($nameInLayout);

        // the names are collected iteratively, a faulty layout with a cycle must not loop endlessly
        while (is_string($parentName) && ! in_array($parentName, $names, true)) {
            array_unshift($names, $parentName);

            $parentName = $layout?->getParentName($parentName);
        }

        return implode('/', $names);
    }
}
