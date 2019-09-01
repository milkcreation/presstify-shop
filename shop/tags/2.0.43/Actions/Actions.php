<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Actions;

use tiFy\Plugins\Shop\{
    Contracts\Actions as ActionsContract,
    AbstractShopSingleton
};
use tiFy\Support\Proxy\Router;

class Actions extends AbstractShopSingleton implements ActionsContract
{
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