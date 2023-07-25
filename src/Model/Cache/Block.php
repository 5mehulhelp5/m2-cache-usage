<?php

namespace Infrangible\CacheUsage\Model\Cache;

use JsonSerializable;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Block
    implements JsonSerializable
{
    /** @var string */
    private $layoutName;

    /** @var string */
    private $className;

    /** @var string */
    private $templateName;

    /** @var bool */
    private $uncacheable = false;

    /** @var bool */
    private $cached = false;

    /** @var bool */
    private $uncached = false;

    /** @var float */
    private $started = 0;

    /** @var float */
    private $finished = 0;

    public function __construct()
    {
        $this->setStarted(microtime(true));
    }

    /**
     * @return string|null
     */
    public function getLayoutName(): ?string
    {
        return $this->layoutName;
    }

    /**
     * @param string|null $layoutName
     */
    public function setLayoutName(?string $layoutName): void
    {
        $this->layoutName = $layoutName;
    }

    /**
     * @return string|null
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @param string|null $className
     */
    public function setClassName(?string $className): void
    {
        $this->className = $className;
    }

    /**
     * @return string|null
     */
    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    /**
     * @param string|null $templateName
     */
    public function setTemplateName(?string $templateName): void
    {
        $this->templateName = $templateName;
    }

    /**
     * @return bool
     */
    public function isUncacheable(): bool
    {
        return $this->uncacheable;
    }

    /**
     * @param bool $uncacheable
     */
    public function setUncacheable(bool $uncacheable): void
    {
        $this->uncacheable = $uncacheable;
    }

    /**
     * @return bool
     */
    public function isCached(): bool
    {
        return $this->cached;
    }

    /**
     * @param bool $cached
     */
    public function setCached(bool $cached): void
    {
        $this->cached = $cached;
    }

    /**
     * @return bool
     */
    public function isUncached(): bool
    {
        return $this->uncached;
    }

    /**
     * @param bool $uncached
     */
    public function setUncached(bool $uncached): void
    {
        $this->uncached = $uncached;
    }

    /**
     * @return float
     */
    public function getStarted(): float
    {
        return $this->started;
    }

    /**
     * @param float $started
     */
    public function setStarted(float $started): void
    {
        $this->started = $started;
    }

    /**
     * @return float
     */
    public function getFinished(): float
    {
        return $this->finished;
    }

    /**
     * @param float $finished
     */
    public function setFinished(float $finished): void
    {
        $this->finished = $finished;
    }

    /**
     * @return float
     */
    public function getDuration(): float
    {
        return round(($this->getFinished() - $this->getStarted()) * 1000);
    }

    /**
     * @return array
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
