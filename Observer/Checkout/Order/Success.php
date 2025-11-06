<?php

namespace Pivotal\Analytics\Observer\Checkout\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Pivotal\Analytics\Logger\Logger;
use Pivotal\Analytics\Helper\Data;

class Success implements ObserverInterface
{
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
     * @param Data $helper
     * @param Logger $logger
     */
    public function __construct(
        Data $helper,
        Logger $logger
    ) {
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
            if ($this->helper->trackPurchaseEvent() === 1) {
                $order = $observer->getEvent()->getOrder();
                $items = $order->getAllVisibleItems();
                $orderItems = [];
                foreach ($items as $item) {
                    $orderItems[] = [
                        'productId' => $item->getProductId(),
                        'productName' => $item->getName(),
                        'price' => $item->getPrice(),
                        'quantity' => $item->getQtyOrdered()
                    ];
                }

                $this->trackPurchase(
                    $order->getIncrementId(),
                    $order->getGrandTotal(),
                    $order->getCustomerEmail(),
                    $orderItems,
                    $order->getCreatedAt()
                );
            }
        } catch (\Exception $e) {
            $this->logger->error('Error in Order Success observer: ' . $e->getMessage());
        }
    }

    /**
     * Handle purchase event for analytic tracking
     *
     * @param integer $orderId
     * @param float $orderTotal
     * @param string $customerEmail
     * @param array $orderItems
     * @param string|null $transactionDate ISO date string for backdating
     * @return void
     */
    private function trackPurchase($orderId, $orderTotal, $customerEmail, $orderItems = [], $transactionDate = null)
    {
        $sessionIds = $this->helper->getSessionIds();

        $data = [
            'eventType' => 'purchase',
            'visitorId' => $sessionIds['visitorId'],
            'sessionId' => $sessionIds['sessionId'],
            'orderId' => $orderId,
            'orderGrandTotal' => $orderTotal,
            'customerEmailAddress' => $customerEmail,
            'orderItems' => $orderItems,
            'transactionDate' => $transactionDate // ISO date string for backdating
        ];

        $this->helper->sendTrackingEvent($data);
    }
}
