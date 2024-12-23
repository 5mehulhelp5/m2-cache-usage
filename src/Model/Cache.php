<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Infrangible\CacheUsage\Model\Cache\Block;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Cache
{
    /** @var string */
    private $routeName;

    /** @var string */
    private $controllerName;

    /** @var string */
    private $actionName;

    /** @var string */
    private $pathParameters;

    /** @var string */
    private $queryParameters;

    /** @var bool */
    private $cacheable = false;

    /** @var bool */
    private $cached = false;

    /** @var Block[] */
    private $blocks = [];

    /** @var float */
    private $started = 0;

    /** @var float */
    private $finished = 0;

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function setRouteName(?string $routeName): void
    {
        $this->routeName = $routeName;
    }

    public function getControllerName(): ?string
    {
        return $this->controllerName;
    }

    public function setControllerName(?string $controllerName): void
    {
        $this->controllerName = $controllerName;
    }

    public function getActionName(): ?string
    {
        return $this->actionName;
    }

    public function setActionName(?string $actionName): void
    {
        $this->actionName = $actionName;
    }

    public function getPathParameters(): ?string
    {
        return $this->pathParameters;
    }

    public function setPathParameters(?string $pathParameters): void
    {
        $this->pathParameters = $pathParameters;
    }

    public function getQueryParameters(): ?string
    {
        return $this->queryParameters;
    }

    public function setQueryParameters(?string $queryParameters): void
    {
        $this->queryParameters = $queryParameters;
    }

    public function isCacheable(): bool
    {
        return $this->cacheable;
    }

    public function setCacheable(bool $cacheable): void
    {
        $this->cacheable = $cacheable;
    }

    public function isCached(): bool
    {
        return $this->cached;
    }

    public function setCached(bool $cached): void
    {
        $this->cached = $cached;
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @param Block[] $blocks
     */
    public function setBlocks(array $blocks): void
    {
        $this->blocks = $blocks;
    }

    public function setBlocksData(array $blocks): void
    {
        foreach ($blocks as $blockName => $blockData) {
            $block = new Block();

            if (array_key_exists('layout_name', $blockData)) {
                $block->setLayoutName($blockData[ 'layout_name' ]);
            }

            if (array_key_exists('class_name', $blockData)) {
                $block->setClassName($blockData[ 'class_name' ]);
            }

            if (array_key_exists('template_name', $blockData)) {
                $block->setTemplateName($blockData[ 'template_name' ]);
            }

            if (array_key_exists('uncacheable', $blockData)) {
                $block->setUncacheable($blockData[ 'uncacheable' ]);
            }

            if (array_key_exists('cached', $blockData)) {
                $block->setCached($blockData[ 'cached' ]);
            }

            if (array_key_exists('uncached', $blockData)) {
                $block->setUncached($blockData[ 'uncached' ]);
            }

            $this->blocks[ $blockName ] = $block;
        }
    }

    /**
     * @return Block[]
     */
    public function getDefaultBlocks(): array
    {
        $blocks = [];

        foreach ($this->getBlocks() as $block) {
            if ( ! $block->isUncacheable() && ! $block->isCached() && ! $block->isUncached()) {
                $blocks[ $block->getLayoutName() ] = $block;
            }
        }

        return $blocks;
    }

    /**
     * @return Block[]
     */
    public function getUncacheableBlocks(): array
    {
        $blocks = [];

        foreach ($this->getBlocks() as $blockName => $block) {
            if ($block->isUncacheable()) {
                $blocks[ $blockName ] = $block;
            }
        }

        return $blocks;
    }

    /**
     * @return Block[]
     */
    public function getCachedBlocks(): array
    {
        $blocks = [];

        foreach ($this->getBlocks() as $block) {
            if ($block->isCached()) {
                $blocks[ $block->getLayoutName() ] = $block;
            }
        }

        return $blocks;
    }

    /**
     * @return Block[]
     */
    public function getUncachedBlocks(): array
    {
        $blocks = [];

        foreach ($this->getBlocks() as $block) {
            if ($block->isUncached()) {
                $blocks[ $block->getLayoutName() ] = $block;
            }
        }

        return $blocks;
    }

    public function addBlockData(
        LayoutInterface $layout,
        string $name,
        string $className,
        string $templateName): Block
    {
        $cacheBlock = $this->getBlock($name);

        $cacheBlock->setLayoutName($this->getLayoutName($layout, $name));
        $cacheBlock->setClassName($className);
        $cacheBlock->setTemplateName($templateName);

        $this->blocks[ $name ] = $cacheBlock;

        return $cacheBlock;
    }

    /**
     * @throws LocalizedException
     */
    public function addBlock(AbstractBlock $block): Block
    {
        $blockName = $block->getNameInLayout();

        if ($blockName === null) {
            $blockName = $this->generateBlockName($block);
        }

        $templateName = '-';

        if ($block instanceof Template) {
            $template = $block->getTemplate();

            if ( ! empty($template)) {
                $templateName = $block->getTemplateFile();
            }
        }

        return $this->addBlockData($block->getLayout(), $blockName, get_class($block), $templateName);
    }

    public function generateBlockName(AbstractBlock $block): string
    {
        $data = [];

        foreach ($block->getData() as $key => $value) {
            if (is_scalar($value)) {
                $data[ $key ] = $value;
            }
        }

        return 'anonymous/' . md5(json_encode($data));
    }

    /**
     * @throws LocalizedException
     */
    public function addUncacheableBlock(AbstractBlock $block): Block
    {
        $cacheBlock = $this->addBlock($block);

        $cacheBlock->setUncacheable(true);

        return $cacheBlock;
    }

    /**
     * @throws LocalizedException
     */
    public function addCachedBlock(AbstractBlock $block): Block
    {
        $cacheBlock = $this->addBlock($block);

        $cacheBlock->setCached(true);

        return $cacheBlock;
    }

    /**
     * @throws LocalizedException
     */
    public function addUncachedBlock(AbstractBlock $block): Block
    {
        $cacheBlock = $this->addBlock($block);

        $cacheBlock->setUncached(true);

        return $cacheBlock;
    }

    public function getBlock(string $blockName): Block
    {
        if (array_key_exists($blockName, $this->blocks)) {
            return $this->blocks[ $blockName ];
        }

        return new Block();
    }

    protected function getLayoutName(LayoutInterface $layout, string $nameInLayout): string
    {
        $parentName = $layout->getParentName($nameInLayout);

        if ($parentName === false) {
            return $nameInLayout;
        }

        return sprintf('%s/%s', $this->getLayoutName($layout, $parentName), $nameInLayout);
    }

    public function getStarted(): float
    {
        return $this->started;
    }

    public function setStarted(float $started): void
    {
        $this->started = $started;
    }

    public function getFinished(): float
    {
        return $this->finished;
    }

    public function setFinished(float $finished): void
    {
        $this->finished = $finished;
    }

    public function getDuration(): float
    {
        return round(($this->getFinished() - $this->getStarted()) * 1000);
    }
}
