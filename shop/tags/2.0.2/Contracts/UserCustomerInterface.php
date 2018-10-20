<?php

namespace tiFy\Plugins\Shop\Contracts;

interface UserCustomerInterface extends UserItemInterface
{
    /**
     * Récupération de la liste des commandes du client
     *
     * @param array $query_args Liste des arguments de requête personnalisée.
     *
     * @return OrderListInterface
     */
    public function getOrderList($query_args = []);
}