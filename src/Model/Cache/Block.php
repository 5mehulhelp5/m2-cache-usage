<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Model\Cache;

use JsonSerializable;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Block implements JsonSerializable
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

    public function getLayoutName(): ?string
    {
        return $this->layoutName;
    }

    public function setLayoutName(?string $layoutName): void
    {
        $this->layoutName = $layoutName;
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function setClassName(?string $className): void
    {
        $this->className = $className;
    }

    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    public function setTemplateName(?string $templateName): void
    {
        $this->templateName = $templateName;
    }

    public function isUncacheable(): bool
    {
        return $this->uncacheable;
    }

    public function setUncacheable(bool $uncacheable): void
    {
        $this->uncacheable = $uncacheable;
    }

    public function isCached(): bool
    {
        return $this->cached;
    }

    public function setCached(bool $cached): void
    {
        $this->cached = $cached;
    }

    public function isUncached(): bool
    {
        return $this->uncached;
    }

    public function setUncached(bool $uncached): void
    {
        $this->uncached = $uncached;
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

    public function getDuration(): int
    {
        return intval(round(($this->getFinished() - $this->getStarted()) * 1000));
    }

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
