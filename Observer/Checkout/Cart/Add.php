<?php

namespace Pivotal\Analytics\Observer\Checkout\Cart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Pivotal\Analytics\Logger\Logger;
use Pivotal\Analytics\Helper\Data;

class Add implements ObserverInterface
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
    public function execute(Observer $observer): void
    {
        try {
            if ($this->helper->trackAddToCartEvent() === 1) {
                $event = $observer->getEvent();
                $product = $event->getProduct();
                $params = $event->getRequest()->getParams();

                $this->trackAddToCart(
                    $product->getSku(),
                    $product->getName(),
                    $product->getFinalPrice(),
                    $params['qty'] ?? 1
                );
            }
        } catch (\Exception $e) {
            $this->logger->error('Error in Add to cart observer: ' . $e->getMessage());
        }
    }

    /**
     * Handle add to cart event for analytic tracking
     *
     * @param integer $productId
     * @param string $productName
     * @param float $price
     * @param integer $quantity
     * @return void
     */
    private function trackAddToCart($productId, $productName, $price, $quantity = 1): void
    {
        $sessionIds = $this->helper->getSessionIds();

        $data = [
            'eventType' => 'addtocart',
            'productId' => $productId,
            'productName' => $productName,
            'price' => $price,
            'quantity' => $quantity,
            'currency' => 'USD',
            'visitorId' => $sessionIds['visitorId'],
            'sessionId' => $sessionIds['sessionId']
        ];

        $this->helper->sendTrackingEvent($data);
    }
}
