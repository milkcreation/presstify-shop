<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

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
     * @param Shop $shop
     *
     * @return static
     */
    public function setShop(Shop $shop);
}