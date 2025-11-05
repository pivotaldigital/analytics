<?php

namespace Pivotal\Analytics\Observer\Sales\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Pivotal\Analytics\Logger\Logger;
use Pivotal\Analytics\Helper\Data;

class Refund implements ObserverInterface
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
            if ($this->helper->trackRefundEvent()) {
                $creditmemo = $observer->getEvent()->getCreditmemo();
                $order = $creditmemo->getOrder();

                $refundAmount = $creditmemo->getGrandTotal();

                $refundedItems = [];
                foreach ($creditmemo->getAllItems() as $item) {
                    $refundedItems[] = [
                        'productId' => $item->getOrderItem()->getItemId(),
                        'productName' => $item->getName(),
                        'refundAmount' => $item->getPrice(),
                        'quantity' => $item->getQty(),
                    ];
                }

                $this->trackRefund(
                    $order->getIncrementId(),
                    $refundAmount,
                    $creditmemo->getCustomerNote() ?? 'admin_refund',
                    ($refundAmount == $order->getGrandTotal()) ? 'full' : 'partial',
                    $refundedItems,
                    $order->getCreatedAt()
                );
            }
        } catch (\Exception $e) {
            $this->logger->error('Error in Refund observer: ' . $e->getMessage());
        }
    }

    /**
     * Track refund event
     *
     * @param string $originalOrderId
     * @param float $refundAmount
     * @param string $refundReason
     * @param string $refundType
     * @param array $refundedItems
     * @param string|null $originalPurchaseDate
     * @return void
     */
    private function trackRefund($originalOrderId, $refundAmount, $refundReason, $refundType = 'partial', $refundedItems = [], $originalPurchaseDate = null)
    {
        $sessionIds = $this->helper->getSessionIds();

        $data = [
            'eventType' => 'refund',
            'visitorId' => $sessionIds['visitorId'],
            'sessionId' => $sessionIds['sessionId'],
            'originalOrderId' => $originalOrderId,
            'refundAmount' => $refundAmount,
            'refundReason' => $refundReason,
            'refundType' => $refundType, // 'partial', 'full', or 'void'
            'refundedItems' => $refundedItems,
            'transactionDate' => $originalPurchaseDate // Use original purchase date!
        ];

        $this->helper->sendTrackingEvent($data);
    }
}
