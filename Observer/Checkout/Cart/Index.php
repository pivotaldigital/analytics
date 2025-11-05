<?php

namespace Pivotal\Analytics\Observer\Checkout\Cart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Pivotal\Analytics\Logger\Logger;
use Pivotal\Analytics\Helper\Data;
use Magento\Checkout\Model\Session;

class Index implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param Session $checkoutSession
     * @param Data $helper
     * @param Logger $logger
     */
    public function __construct(
        Session $checkoutSession,
        Data $helper,
        Logger $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            if ($this->helper->trackViewCartEvent() === 1) {
                $quote = $this->checkoutSession->getQuote();
                $items = $quote->getAllVisibleItems();
                $cartItems = [];
                foreach ($items as $item) {
                    $cartItems[] = [
                        'id' => $item->getProductId(),
                        'name' => $item->getProduct()->getName(),
                        'price' => $item->getCalculationPrice(),
                        'quantity' => $item->getQty()
                    ];
                }

                if ($quote->isVirtual()) {
                    $totals = $quote->getBillingAddress()->getTotals();
                } else {
                    $totals = $quote->getShippingAddress()->getTotals();
                }

                if (!empty($totals['subtotal']) && $totals['subtotal']->getData('value')) {
                    $this->trackViewCart($totals['subtotal']->getData('value'), count($cartItems), $cartItems);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Error in View Cart observer: ' . $e->getMessage());
        }
    }

    private function trackViewCart($cartTotal, $itemCount, $items = [])
    {
        $sessionIds = $this->helper->getSessionIds();

        $data = [
            'eventType' => 'viewcart',
            'cartTotal' => $cartTotal,
            'visitorId' => $sessionIds['visitorId'],
            'sessionId' => $sessionIds['sessionId'],
            'customProperties' => [
                'itemCount' => $itemCount,
                'items' => $items
            ]
        ];

        $this->helper->sendTrackingEvent($data);
    }
}
