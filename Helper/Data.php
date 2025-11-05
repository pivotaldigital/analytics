<?php

namespace Pivotal\Analytics\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'pivotal_analytics/general/enabled';
    const XML_PATH_SITE_KEY = 'pivotal_analytics/general/site_key';
    const XML_PATH_TRACK_ADD_CART = 'pivotal_analytics/general/track_add_cart';
    const XML_PATH_TRACK_VIEW_CART = 'pivotal_analytics/general/track_view_cart';
    const XML_PATH_TRACK_PURCHASE = 'pivotal_analytics/general/track_purchase';
    const XML_PATH_TRACK_REFUND = 'pivotal_analytics/general/track_refund';
    const XML_PATH_TRACK_CANCELLATION = 'pivotal_analytics/general/track_cancellation';

    const PIVOTAL_ANALYTICS_FUNCTION_IDENTIFIER = 'gkvutfqqebfflznekqat';

    const PIVOTAL_ANALYTICS_API_URL = "https://".self::PIVOTAL_ANALYTICS_FUNCTION_IDENTIFIER.".supabase.co/functions/v1/server-track";
    const PIVOTAL_ANALYTICS_COOKIE_VISITOR_ID = 'pv_id';
    const PIVOTAL_ANALYTICS_COOKIE_SESSION_ID = 'ps_id';

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
            'visitorId' => $_COOKIE[self::PIVOTAL_ANALYTICS_COOKIE_VISITOR_ID] ?? null,
            'sessionId' => $_COOKIE[self::PIVOTAL_ANALYTICS_COOKIE_SESSION_ID] ?? null
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
        if (!$this->isEnabled() || !($siteKey = $this->getSiteKey())) {
            return false;
        }

        $data['siteKey'] = $siteKey;

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($data)
            ]
        ];
        
        $context = stream_context_create($options);
        return file_get_contents(
            self::PIVOTAL_ANALYTICS_API_URL,
            false,
            $context
        );
    }

    /**
     * Check if track add to cart event is enabled
     *
     * @return int|false
     */
    public function trackViewCartEvent(): int|false
    {
        if(!$this->isEnabled() || !$this->getSiteKey()) {
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
        if(!$this->isEnabled() || !$this->getSiteKey()) {
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
        if(!$this->isEnabled() || !$this->getSiteKey()) {
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
        if(!$this->isEnabled() || !$this->getSiteKey()) {
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
        if(!$this->isEnabled() || !$this->getSiteKey()) {
            return false;
        }

        return (int) $this->scopeConfig->isSetFlag(
            self::XML_PATH_TRACK_CANCELLATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
    