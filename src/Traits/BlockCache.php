<?php

namespace Infrangible\CacheUsage\Traits;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
trait BlockCache
{
    /**
     * @return string
     */
    protected function getModuleKey(): string
    {
        return 'Infrangible_CacheUsage';
    }

    /**
     * @return string
     */
    protected function getResourceKey(): string
    {
        return 'infrangible_cache_usage';
    }

    /**
     * @return string
     */
    protected function getMenuKey(): string
    {
        return 'infrangible_cache_usage_block';
    }

    /**
     * @return string
     */
    protected function getTitle(): string
    {
        return __('Block Cache');
    }

    /**
     * @return string
     */
    protected function getObjectName(): string
    {
        return 'BlockCache';
    }

    /**
     * @return bool
     */
    protected function allowAdd(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    protected function allowEdit(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    protected function allowView(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    protected function allowDelete(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getObjectNotFoundMessage(): string
    {
        return __('Unable to find block cache with id: %s!');
    }
}
