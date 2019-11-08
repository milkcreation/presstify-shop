<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface CartSessionItemsInterface extends ParamsBag
{
    /**
     * Récupération de l'instance du panier.
     *
     * @return CartInterface
     */
    public function cart(): CartInterface;

    /**
     * Détruit les données de session associées au panier.
     *
     * @param bool $persistent Active la suppression des données de panier relatives aux options utilisateur
     *
     * @return void
     */
    public function destroy($persistent = true): void;

    /**
     * Retrouve les articles du panier portés par la session.
     *
     * @return void
     */
    public function fetchCart(): void;

    /**
     * Mise à jour des données de session.
     *
     * @return void
     */
    public function update(): void;
}