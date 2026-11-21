<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Model\Cache;

use JsonSerializable;

/**
 * A block of the current request: its names, how it is cached and how long it took to render.
 */
class Block implements JsonSerializable
{
    use Timing;

    /** Cache state of a block that the layout declares as not cacheable. */
    public const STATE_UNCACHEABLE = 'uncacheable';

    /** Cache state of a block that was loaded from the block cache. */
    public const STATE_CACHED = 'cached';

    /** Cache state of a cacheable block that was not in the block cache. */
    public const STATE_UNCACHED = 'uncached';

    /** Cache state of a block without own cache, which is only cached with the page. */
    public const STATE_DEFAULT = 'default';

    /** @var string|null */
    private ?string $layoutName = null;

    /** @var string|null */
    private ?string $className = null;

    /** @var string|null */
    private ?string $templateName = null;

    /** @var bool */
    private bool $uncacheable = false;

    /** @var bool */
    private bool $cached = false;

    /** @var bool */
    private bool $uncached = false;

    /**
     * Returns the layout name.
     *
     * @return string|null
     */
    public function getLayoutName(): ?string
    {
        return $this->layoutName;
    }

    /**
     * Sets the layout name.
     *
     * @param string|null $layoutName
     *
     * @return void
     */
    public function setLayoutName(?string $layoutName): void
    {
        $this->layoutName = $layoutName;
    }

    /**
     * Returns the class name.
     *
     * @return string|null
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * Sets the class name.
     *
     * @param string|null $className
     *
     * @return void
     */
    public function setClassName(?string $className): void
    {
        $this->className = $className;
    }

    /**
     * Returns the template name.
     *
     * @return string|null
     */
    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    /**
     * Sets the template name.
     *
     * @param string|null $templateName
     *
     * @return void
     */
    public function setTemplateName(?string $templateName): void
    {
        $this->templateName = $templateName;
    }

    /**
     * Checks if the block is uncacheable.
     *
     * @return bool
     */
    public function isUncacheable(): bool
    {
        return $this->uncacheable;
    }

    /**
     * Sets the uncacheable.
     *
     * @param bool $uncacheable
     *
     * @return void
     */
    public function setUncacheable(bool $uncacheable): void
    {
        $this->uncacheable = $uncacheable;
    }

    /**
     * Checks if the block is cached.
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
     * Checks if the block is uncached.
     *
     * @return bool
     */
    public function isUncached(): bool
    {
        return $this->uncached;
    }

    /**
     * Sets the uncached.
     *
     * @param bool $uncached
     *
     * @return void
     */
    public function setUncached(bool $uncached): void
    {
        $this->uncached = $uncached;
    }

    /**
     * Returns the cache state of the block, one of the STATE constants.
     *
     * @return string
     */
    public function getState(): string
    {
        return match (true) {
            $this->isUncacheable() => self::STATE_UNCACHEABLE,
            $this->isCached() => self::STATE_CACHED,
            $this->isUncached() => self::STATE_UNCACHED,
            default => self::STATE_DEFAULT
        };
    }

    /**
     * Returns the data that the full page cache stores with the page.
     *
     * @return array<string, string|bool|null>
     */
    public function jsonSerialize(): array
    {
        return [
            'layout_name'   => $this->getLayoutName(),
            'class_name'    => $this->getClassName(),
            'template_name' => $this->getTemplateName(),
            'uncacheable'   => $this->isUncacheable(),
            'cached'        => $this->isCached(),
            'uncached'      => $this->isUncached()
        ];
    }
}
