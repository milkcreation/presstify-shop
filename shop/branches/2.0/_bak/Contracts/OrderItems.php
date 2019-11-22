<?php declare(strict_types=1);

namespace __tiFy\Plugins\Shop\Contracts;

interface OrderItems
{
    /**
     * Suppression de la liste des éléments.
     *
     * @param string|null $type
     *
     * @return void
     */
    public function delete(?string $type = null): void;

    /**
     * Récupération d'un élément.
     *
     * @param int|object $id Identifiant de qualification de l'objet|Instance de l'objet.
     *
     * @return OrderItem|null
     */
    public function get($id = null): ?OrderItem;

    /**
     * Récupération des données d'une liste d'éléments selon des critères de requête.
     *
     * @param array $query_args Liste des arguments de requête.
     *
     * @return OrderItemsCollection|OrderItem[]|array
     */
    public function query($query_args = []): OrderItemsCollection;
}