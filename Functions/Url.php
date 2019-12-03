<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Functions;

use League\Uri;
use tiFy\Plugins\Shop\Contracts\{FunctionsUrl as FunctionsUrlContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;

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
        $base_uri = Uri\create($this->checkoutPage());

        return (string)Uri\append_query($base_uri, http_build_query($args));
    }

    /**
     * @inheritDoc
     */
    public function checkoutOrderReceivedPage(array $args = []): string
    {
        $base_uri = Uri\create($this->checkoutPage());

        return (string)Uri\append_query($base_uri, http_build_query($args));
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