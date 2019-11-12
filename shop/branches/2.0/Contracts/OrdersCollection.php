<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\Collection;

interface OrdersCollection extends Collection, ShopAwareTrait
{
    /**
     * Récupération du nombre d'enregistrement total.
     *
     * @return int
     */
    public function getTotal(): int;

    /**
     * Requête de récupération de la liste des éléments.
     *
     * @param array ...$args Liste des arguments dynamiques passés à la méthode.
     *
     * @return static
     */
    public function query(...$args): OrdersCollection;

    /**
     * Définition du nombre d'enregistrement total.
     *
     * @param int $total
     *
     * @return $this
     */
    public function setTotal($total): OrdersCollection;
}