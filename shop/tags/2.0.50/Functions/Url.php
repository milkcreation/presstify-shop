<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Plugins\Shop\Contracts\{FunctionsUrl as FunctionsUrlContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Proxy\Url as UrlProxy;

class Url implements FunctionsUrlContract
{
    use ShopAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);
    }

    /**
     * @inheritDoc
     */
    public function cartPage(): string
    {
        return ($page_id = $this->shop()->settings()->cartPageId()) ? get_permalink($page_id) : get_home_url();
    }

    /**
     * @inheritDoc
     */
    public function checkoutAddPaymentMethodPage(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function checkoutDeletePaymentMethodPage(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function checkoutPage(): string
    {
        return ($page_id = $this->shop()->settings()->checkoutPageId()) ? get_permalink($page_id) : get_home_url();
    }

    /**
     * @inheritDoc
     */
    public function checkoutOrderPayPage(array $args = []): string
    {
        return UrlProxy::set($this->checkoutPage())->with($args)->render();
    }

    /**
     * @inheritDoc
     */
    public function checkoutOrderReceivedPage(array $args = []): string
    {
        return UrlProxy::set($this->checkoutPage())->with($args)->render();
    }

    /**
     * @inheritDoc
     */
    public function checkoutSetDefaultPaymentMethodPage(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function page(string $name): string
    {
        $method = strtolower($name) . "Page";
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function shopPage(): string
    {
        return ($page_id = $this->shop()->settings()->shopPageId()) ? get_permalink($page_id) : get_home_url();
    }

    /**
     * @inheritDoc
     */
    public function termsPage(): string
    {
        return ($page_id = $this->shop()->settings()->termsPageId()) ? get_permalink($page_id) : get_home_url();
    }
}