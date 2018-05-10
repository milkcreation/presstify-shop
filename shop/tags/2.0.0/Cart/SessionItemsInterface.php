<?php

namespace tiFy\Plugins\Shop\Cart;

interface SessionItemsInterface
{
    /**
     * Récupération des articles du panier portés par la session.
     *
     * @return void
     */
    public function getCart();

    /**
     * Détruit les données de session associées au panier.
     *
     * @param bool $persistent Active la suppression des données de panier relatives aux options utilisateur
     *
     * @return void
     */
    public function destroy($persistent = true);

    /**
     * Mise à jour des données de session.
     *
     * @return void
     */
    public function update();
}