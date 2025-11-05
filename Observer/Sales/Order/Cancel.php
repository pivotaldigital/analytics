<?php

namespace Pivotal\Analytics\Observer\Sales\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Pivotal\Analytics\Logger\Logger;
use Pivotal\Analytics\Helper\Data;

class Cancel implements ObserverInterface
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
            if ($this->helper->trackCancellationEvent()) {
                $order = $observer->getEvent()->getOrder();

                $amount = $order->getGrandTotal();

                $orderItems = [];
                foreach ($order->getAllItems() as $item) {
                    $orderItems[] = [
                        'productId' => $item->getProductId(),
                        'productName' => $item->getName(),
                        'quantity' => $item->getQty(),
                    ];
                }

                $this->trackCancellation(
                    $order->getIncrementId(),
                    $amount,
                    'admin_order_cancellation',
                    $orderItems,
                    $order->getCreatedAt()
                );
            }
        } catch (\Exception $e) {
            $this->logger->error('Error in order cancellation observer: ' . $e->getMessage());
        }
    }

    /**
     * Track cancellation event
     *
     * @param string $orderId
     * @param float $cancelledAmount
     * @param string $cancellationReason
     * @param array $cancelledItems
     * @param string|null $originalPurchaseDate
     * @return void
     */
    private function trackCancellation($orderId, $cancelledAmount, $cancellationReason, $cancelledItems = [], $originalPurchaseDate = null) 
    {
        $sessionIds = $this->helper->getSessionIds();

        $data = [
            'eventType' => 'cancellation',
            'visitorId' => $sessionIds['visitorId'],
            'sessionId' => $sessionIds['sessionId'],
            'orderId' => $orderId,
            'cancelledAmount' => $cancelledAmount,
            'cancellationReason' => $cancellationReason,
            'cancelledItems' => $cancelledItems,
            'transactionDate' => $originalPurchaseDate
        ];

        $this->helper->sendTrackingEvent($data);
    }
}
