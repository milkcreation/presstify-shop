<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use Illuminate\Database\Query\Builder;
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

    /**
     * Récupération d'une instance du gestionnaire de requête des éléments associés à une commande.
     *
     * @return Builder
     */
    public function orderItemsTable(): Builder;

    /**
     * Récupération d'une instance du gestionnaire de requête des métadonnées d'éléments associés à une commande.
     *
     * @return Builder
     */
    public function orderItemMetaTable(): Builder;
}