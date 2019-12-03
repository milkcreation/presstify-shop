<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface CartSession extends ParamsBag, ShopAwareTrait
{
    /**
     * Récupération de l'instance du panier.
     *
     * @return Cart
     */
    public function cart(): Cart;

    /**
     * Détruit les données de session associées au panier.
     *
     * @param bool $persistent Active la suppression des données de panier relatives aux options utilisateur
     *
     * @return void
     */
    public function destroy($persistent = true): CartSession;

    /**
     * Retrouve les articles du panier portés par la session.
     *
     * @return void
     */
    public function fetchCart(): CartSession;

    /**
     * Mise à jour des données de session.
     *
     * @return void
     */
    public function update(): CartSession;
}