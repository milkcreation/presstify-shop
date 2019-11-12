<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\Collection;

interface OrderItemsCollection extends Collection, ShopAwareTrait
{
    /**
     * Requête de récupération de la liste des éléments.
     *
     * @param array ...$args Liste des arguments dynamiques passés à la méthode.
     *
     * @return static
     */
    public function query(...$args): OrderItemsCollection;
}