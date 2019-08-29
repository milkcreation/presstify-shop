<?php

namespace tiFy\Plugins\Shop;

use tiFy\Plugins\Shop\{
    Contracts\ShopInterface as ShopContract,
    Contracts\ShopResolverInterface
};

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
     * @param ShopContract $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    protected function __construct(ShopContract $shop)
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
     * @param ShopContract $shop Instance de la boutique
     *
     * @return static
     */
    public static function make($alias, ShopContract $shop)
    {
        return self::$instances[$alias] = self::$instances[$alias] ?? new static($shop);
    }
}