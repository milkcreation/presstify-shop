<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface UserCustomer extends User
{
    /**
     * Récupération de la liste des commandes du client
     *
     * @param array $args Liste des arguments de requête personnalisée.
     *
     * @return array
     */
    public function getOrders(array $args = []): array;
}