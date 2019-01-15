<?php

namespace tiFy\Plugins\Shop;

use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;

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
     * @return void
     */
    protected function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
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
        return self::$instances[$alias] = self::$instances[$alias] ?? new static($shop);
    }
}