<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Model\Config\Source;

use Infrangible\CacheUsage\Model\Recorder;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Options of the column "cacheable" of the blocks: blocks without own cache are only cached with the page.
 */
class Cacheable implements OptionSourceInterface
{
    /**
     * Returns the options.
     *
     * @return array<int, array{value: int, label: string}>
     */
    public function toOptionArray(): array
    {
        return array_map(
            static fn(int $value, string $label): array => ['value' => $value, 'label' => $label],
            array_keys($this->toOptions()),
            $this->toOptions()
        );
    }

    /**
     * Returns the labels by value.
     *
     * @return array<int, string>
     */
    public function toOptions(): array
    {
        return [
            0                             => __('No')->render(),
            1                             => __('Yes')->render(),
            Recorder::CACHEABLE_UNDEFINED => __('Only with Page')->render()
        ];
    }
}
