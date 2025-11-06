<?php

namespace Pivotal\Analytics\Helper;

use Facebook\WebDriver\Cookie;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Pivotal\Analytics\Logger\Logger;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private const XML_PATH_ENABLED = 'pivotal_analytics/general/enabled';
    private const XML_PATH_SITE_KEY = 'pivotal_analytics/general/site_key';
    private const XML_PATH_TRACK_ADD_CART = 'pivotal_analytics/general/track_add_cart';
    private const XML_PATH_TRACK_VIEW_CART = 'pivotal_analytics/general/track_view_cart';
    private const XML_PATH_TRACK_PURCHASE = 'pivotal_analytics/general/track_purchase';
    private const XML_PATH_TRACK_REFUND = 'pivotal_analytics/general/track_refund';
    private const XML_PATH_TRACK_CANCELLATION = 'pivotal_analytics/general/track_cancellation';

    public const PIVOTAL_ANALYTICS_FUNCTION_IDENTIFIER = 'gkvutfqqebfflznekqat';

    private const PIVOTAL_ANALYTICS_API_URL =
    "https://" . self::PIVOTAL_ANALYTICS_FUNCTION_IDENTIFIER . ".supabase.co/functions/v1/server-track";

    private const PIVOTAL_ANALYTICS_COOKIE_VISITOR_ID = 'pv_id';
    private const PIVOTAL_ANALYTICS_COOKIE_SESSION_ID = 'ps_id';

    /**
     * @var CookieManagerInterface
     */
    protected CookieManagerInterface $cookieManager;

    /**
     * @var ClientInterface
     */
    protected ClientInterface $httpClient;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param CookieManagerInterface $cookieManager
     * @param ClientInterface $httpClient
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        CookieManagerInterface $cookieManager,
        ClientInterface $httpClient,
        Logger $logger
    ) {
        $this->cookieManager = $cookieManager;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Check if the Pivotal Analytics module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get site key from configuration
     *
     * @return null|string
     */
    public function getSiteKey(): null|string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get function identifier
     *
     * @return string
     */
    public function getFunctionIdentifier(): string
    {
        return self::PIVOTAL_ANALYTICS_FUNCTION_IDENTIFIER;
    }

    /**
     * Get cookies array
     *
     * @return array
     */
    public function getSessionIds(): array
    {
        return [
            'visitorId' => $this->cookieManager->getCookie(self::PIVOTAL_ANALYTICS_COOKIE_VISITOR_ID, null),
            'sessionId' => $this->cookieManager->getCookie(self::PIVOTAL_ANALYTICS_COOKIE_SESSION_ID, null)
        ];
    }

    /**
     * Send tracking event to Pivotal Analytics API
     *
     * @param array $data
     * @return string|false
     */
    public function sendTrackingEvent(array $data): string|false
    {
        try {
            if (!$this->isEnabled() || !($siteKey = $this->getSiteKey())) {
                return false;
            }

            $data['siteKey'] = $siteKey;

            $this->httpClient->addHeader('Content-Type', 'application/json');
            $this->httpClient->post(self::PIVOTAL_ANALYTICS_API_URL, json_encode($data));

            return $this->httpClient->getBody();
        } catch (\Exception $e) {
            $this->logger->error('Pivotal Analytics Tracking Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if track add to cart event is enabled
     *
     * @return int|false
     */
    public function trackViewCartEvent(): int|false
    {
        if (!$this->isEnabled() || !$this->getSiteKey()) {
            return false;
        }

        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_TRACK_VIEW_CART,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if track add to cart event is enabled
     *
     * @return int|false
     */
    public function trackAddToCartEvent(): int|false
    {
        if (!$this->isEnabled() || !$this->getSiteKey()) {
            return false;
        }

        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_TRACK_ADD_CART,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if track purchase event is enabled
     *
     * @return int|false
     */
    public function trackPurchaseEvent(): int|false
    {
        if (!$this->isEnabled() || !$this->getSiteKey()) {
            return false;
        }

        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_TRACK_PURCHASE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if track refund event is enabled
     *
     * @return int|false
     */
    public function trackRefundEvent(): bool
    {
        if (!$this->isEnabled() || !$this->getSiteKey()) {
            return false;
        }

        return (int) $this->scopeConfig->isSetFlag(
            self::XML_PATH_TRACK_REFUND,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if track order cancellation event is enabled
     *
     * @return int|false
     */
    public function trackCancellationEvent(): bool
    {
        if (!$this->isEnabled() || !$this->getSiteKey()) {
            return false;
        }

        return (int) $this->scopeConfig->isSetFlag(
            self::XML_PATH_TRACK_CANCELLATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
