<?php

namespace Infrangible\CacheUsage\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2023 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Cacheable
    implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('Yes')],
            ['value' => 2, 'label' => __('Undefined')]
        ];
    }

    /**
     * @return array
     */
    public function toOptions(): array
    {
        return [
            0 => __('No'),
            1 => __('Yes'),
            2 => __('Undefined')
        ];
    }
}
