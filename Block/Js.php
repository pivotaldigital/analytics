<?php

namespace Pivotal\Analytics\Block;

class Js extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Pivotal\Analytics\Helper\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Pivotal\Analytics\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Pivotal\Analytics\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get site key from configuration
     *
     * @return null|string
     */
    public function getSiteKey(): null|string
    {
        return $this->helper->getSiteKey();
    }

    /**
     * Check Pivotal Analytics is enabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->helper->isEnabled() || !$this->helper->getSiteKey()) {
            return '';
        }
        return parent::_toHtml();
    }
}
