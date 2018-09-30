<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Kernel\ParametersBagIteratorInterface;

interface CartSessionItemsInterface extends ParametersBagIteratorInterface
{
    /**
     * Détruit les données de session associées au panier.
     *
     * @param bool $persistent Active la suppression des données de panier relatives aux options utilisateur
     *
     * @return void
     */
    public function destroy($persistent = true);

    /**
     * Récupération des articles du panier portés par la session.
     *
     * @return void
     */
    public function getCart();

    /**
     * Mise à jour des données de session.
     *
     * @return void
     */
    public function update();
}