<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Actions;

use tiFy\Plugins\Shop\Contracts\{Actions as ActionsContract, ShopInterface as Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Proxy\Router;

class Actions implements ActionsContract
{
    use ShopAwareTrait;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function url($alias, $parameters = [], $absolute = false): string
    {
        return Router::url("shop.{$alias}", $parameters, $absolute) ?? '';
    }
}