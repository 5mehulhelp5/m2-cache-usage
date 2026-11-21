<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Test\Unit\Model\Cache;

use Infrangible\CacheUsage\Model\Cache\Block;
use PHPUnit\Framework\TestCase;

/**
 * Tests the cache state and the duration of a block.
 */
class BlockTest extends TestCase
{
    /**
     * Tests that a block that the layout declares as not cacheable counts as uncacheable, also if it was cached.
     *
     * @return void
     */
    public function testGetState(): void
    {
        $block = new Block();

        $this->assertSame(Block::STATE_DEFAULT, $block->getState());

        $block->setUncached(true);

        $this->assertSame(Block::STATE_UNCACHED, $block->getState());

        $block->setCached(true);

        $this->assertSame(Block::STATE_CACHED, $block->getState());

        $block->setUncacheable(true);

        $this->assertSame(Block::STATE_UNCACHEABLE, $block->getState());
    }

    /**
     * Tests that a block that was not rendered has no duration instead of a negative one.
     *
     * @return void
     */
    public function testGetDuration(): void
    {
        $block = new Block();

        $this->assertNull($block->getDuration());

        $block->setStarted(5.0);

        $this->assertNull($block->getDuration());

        $block->setFinished(5.0124);

        $this->assertSame(12, $block->getDuration());

        $block->setFinished(4.0);

        $this->assertSame(0, $block->getDuration());
    }
}
