<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Task;

/**
 * Removes the collected cache usage of pages and blocks that is older than 30 days. Runs hourly as cron job.
 */
class Reduce extends Clear
{
    /** Days the cache usage is kept. */
    public const KEEP_DAYS = 30;

    /**
     * Returns the condition of the rows older than the kept days. The times are stored in GMT.
     *
     * @return string
     */
    protected function getCondition(): string
    {
        return $this->databaseHelper->getDefaultConnection()->quoteInto(
            'created_at <= ?',
            gmdate('Y-m-d H:i:s', time() - self::KEEP_DAYS * 86400)
        );
    }
}
