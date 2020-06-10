<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use Illuminate\Database\Query\Builder;
use tiFy\Contracts\PostType\PostTypeStatus;

interface ShopEntity extends ShopAwareTrait
{
    /**
     * Initialisation.
     *
     * @return static
     */
    public function boot(): ShopEntity;

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

    /**
     * Définition d'un type de statut de commande.
     *
     * @param string $alias
     * @param PostTypeStatus $status
     *
     * @return static
     */
    public function setOrderStatus(string $alias, PostTypeStatus $status): ShopEntity;
}