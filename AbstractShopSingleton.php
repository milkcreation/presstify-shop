<?php

namespace tiFy\Plugins\Shop;

use tiFy\Contracts\App\AppInterface;
use tiFy\Plugins\Shop\Contracts\AddressesInterface;
use tiFy\Plugins\Shop\Contracts\AdminInterface;
use tiFy\Plugins\Shop\Contracts\CartInterface;
use tiFy\Plugins\Shop\Contracts\CheckoutInterface;
use tiFy\Plugins\Shop\Contracts\CustomTypesInterface;
use tiFy\Plugins\Shop\Contracts\FunctionsInterface;
use tiFy\Plugins\Shop\Contracts\GatewaysInterface;
use tiFy\Plugins\Shop\Contracts\NoticesInterface;
use tiFy\Plugins\Shop\Contracts\OrdersInterface;
use tiFy\Plugins\Shop\Contracts\ProductsInterface;
use tiFy\Plugins\Shop\Contracts\SessionInterface;
use tiFy\Plugins\Shop\Contracts\SettingsInterface;
use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;
use tiFy\Plugins\Shop\Contracts\UsersInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

abstract class AbstractShopSingleton implements ShopResolverInterface
{
    use ShopResolverTrait;

    /**
     * Liste des instances existantes.
     * @var array
     */
    protected static $instances = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return null
     */
    protected function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return null
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return null
     */
    private function __wakeup()
    {

    }

    /**
     * Instanciation de la classe.
     *
     * @param string $alias Nom de qualification de la classe.
     * @param Shop $shop Instance de la boutique
     *
     * @return static
     */
    public static function make($alias, Shop $shop)
    {
        if (isset(self::$instances[$alias])) :
            return self::$instances[$alias];
        endif;

        return self::$instances[$alias] = new static($shop);
    }
}