<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface CartSession extends ParamsBag, ShopAwareTrait
{
    /**
     * Récupération de l'instance du panier associé.
     *
     * @return Cart|null
     */
    public function cart(): ?Cart;

    /**
     * Détruit les données de session associées au panier.
     *
     * @param bool $persistent Active la suppression des données de panier relatives aux options utilisateur
     *
     * @return static
     */
    public function destroy($persistent = true): CartSession;

    /**
     * Retrouve les articles du panier portés par la session.
     *
     * @return static
     */
    public function fetchCart(): CartSession;

    /**
     * Mise à jour des données de session.
     *
     * @return static
     */
    public function update(): CartSession;

    /**
     * Définition du panier associé.
     *
     * @param Cart $cart
     *
     * @return static
     */
    public function setCart(Cart $cart): CartSession;
}