<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Plugins\Shop\Contracts\PageFunctions as PageFunctionsContract;
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Proxy\Url;

class PageFunctions implements PageFunctionsContract
{
    use ShopAwareTrait;

    /**
     * @inheritDoc
     */
    public function addPaymentMethodPageUrl(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function cartPageUrl(): string
    {
        return ($page_id = $this->shop()->settings()->cartPageId()) ? get_permalink($page_id) : get_home_url();
    }

    /**
     * @inheritDoc
     */
    public function deletePaymentMethodPageUrl(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function checkoutPageUrl(): string
    {
        return ($page_id = $this->shop()->settings()->checkoutPageId()) ? get_permalink($page_id) : get_home_url();
    }

    /**
     * @inheritDoc
     */
    public function orderPayPageUrl(array $args = []): string
    {
        return Url::set($this->checkoutPageUrl())->with($args)->render();
    }

    /**
     * @inheritDoc
     */
    public function orderReceivedPageUrl(array $args = []): string
    {
        return Url::set($this->checkoutPageUrl())->with($args)->render();
    }

    /**
     * @inheritDoc
     */
    public function shopPageUrl(): string
    {
        return ($page_id = $this->shop()->settings()->shopPageId()) ? get_permalink($page_id) : get_home_url();
    }

    /**
     * @inheritDoc
     */
    public function termsPageUrl(): string
    {
        return ($page_id = $this->shop()->settings()->termsPageId()) ? get_permalink($page_id) : get_home_url();
    }

    /**
     * @inheritDoc
     */
    public function isCart(): bool
    {
        return is_single($this->shop->settings()->cartPageId());
    }

    /**
     * @inheritDoc
     */
    public function isCheckout(): bool
    {
        return is_single($this->shop->settings()->checkoutPageId());
    }

    /**
     * @inheritDoc
     */
    public function isShop(): bool
    {
        return is_single($this->shop->settings()->shopPageId());
    }

    /**
     * @inheritDoc
     */
    public function isTerms(): bool
    {
        return is_single($this->shop->settings()->termsPageId());
    }
}