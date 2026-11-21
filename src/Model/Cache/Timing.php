<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Model\Cache;

/**
 * Start and end time of the request or of a block, and the duration between them.
 */
trait Timing
{
    /** @var float|null */
    private ?float $started = null;

    /** @var float|null */
    private ?float $finished = null;

    /**
     * Returns the start time in seconds, or null.
     *
     * @return float|null
     */
    public function getStarted(): ?float
    {
        return $this->started;
    }

    /**
     * Sets the start time in seconds.
     *
     * @param float|null $started
     *
     * @return void
     */
    public function setStarted(?float $started): void
    {
        $this->started = $started;
    }

    /**
     * Returns the end time in seconds, or null.
     *
     * @return float|null
     */
    public function getFinished(): ?float
    {
        return $this->finished;
    }

    /**
     * Sets the end time in seconds.
     *
     * @param float|null $finished
     *
     * @return void
     */
    public function setFinished(?float $finished): void
    {
        $this->finished = $finished;
    }

    /**
     * Returns the measured time in milliseconds, or null if it was not measured.
     *
     * @return int|null
     */
    public function getDuration(): ?int
    {
        if ($this->started === null || $this->finished === null) {
            return null;
        }

        return max(0, (int)round(($this->finished - $this->started) * 1000));
    }
}
