<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Traits;

/**
 * Settings of the grid of the block cache usage for its controllers: only viewing and deleting.
 */
trait BlockCache
{
    /**
     * Returns the key of the module.
     *
     * @return string
     */
    protected function getModuleKey(): string
    {
        return 'Infrangible_CacheUsage';
    }

    /**
     * Returns the key of the ACL resource.
     *
     * @return string
     */
    protected function getResourceKey(): string
    {
        return 'infrangible_cache_usage';
    }

    /**
     * Returns the key of the menu entry.
     *
     * @return string
     */
    protected function getMenuKey(): string
    {
        return 'infrangible_cache_usage_block';
    }

    /**
     * Returns the title of the grid.
     *
     * @return string
     */
    protected function getTitle(): string
    {
        return __('Block Cache')->render();
    }

    /**
     * Returns the name of the model.
     *
     * @return string
     */
    protected function getObjectName(): string
    {
        return 'BlockCache';
    }

    /**
     * Entries cannot be added.
     *
     * @return bool
     */
    protected function allowAdd(): bool
    {
        return false;
    }

    /**
     * Entries cannot be edited.
     *
     * @return bool
     */
    protected function allowEdit(): bool
    {
        return false;
    }

    /**
     * Entries cannot be viewed.
     *
     * @return bool
     */
    protected function allowView(): bool
    {
        return false;
    }

    /**
     * Entries can be deleted.
     *
     * @return bool
     */
    protected function allowDelete(): bool
    {
        return true;
    }

    /**
     * Returns the message for a missing entry, with %s for its ID.
     *
     * @return string
     */
    protected function getObjectNotFoundMessage(): string
    {
        return __('Unable to find the block cache entry with ID: %s')->render();
    }
}
