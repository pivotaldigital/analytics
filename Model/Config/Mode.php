<?php
namespace Pivotal\Analytics\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class Mode implements OptionSourceInterface
{
    /**
     * Get config options
     *
     * @return void
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('Server side')],
        ];
    }
}
