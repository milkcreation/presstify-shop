<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\PostType\PostTypeStatus;

interface ShopEntity extends ShopAwareTrait
{
    /**
     * Instanciation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Récupération des instances de statuts de commandes.
     *
     * @return PostTypeStatus[]|array
     */
    public function getOrderStatuses(): array;
}