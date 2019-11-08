<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Plugins\Shop\Contracts\ShopInterface as Shop;

interface ShopAwareTrait
{
    /**
     * Récupération de l'instance de la boutique.
     *
     * @return Shop|null
     */
    public function shop(): ?Shop;

    /**
     * Définition du gestionnaire de boutique.
     *
     * @return static
     */
    public function setShop(Shop $shop): ShopAwareTrait;
}