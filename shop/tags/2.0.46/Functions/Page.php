<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Plugins\Shop\Contracts\{FunctionsPage as FunctionsPageContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;

class Page implements FunctionsPageContract
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
    public function is(string $name): bool
    {
        $method = "is" . ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isCart(): bool
    {
        return is_single($this->shop()->settings()->cartPageId());
    }

    /**
     * @inheritDoc
     */
    public function isCheckout(): bool
    {
        return is_single($this->shop()->settings()->checkoutPageId());
    }

    /**
     * @inheritDoc
     */
    public function isShop(): bool
    {
        return is_single($this->shop()->settings()->shopPageId());
    }

    /**
     * @inheritDoc
     */
    public function isTerms(): bool
    {
        return is_single($this->shop()->settings()->termsPageId());
    }
}