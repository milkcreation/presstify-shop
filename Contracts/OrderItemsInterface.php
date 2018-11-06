<?php

namespace tiFy\Plugins\Shop\Contracts;

interface OrderItemsInterface
{
    /**
     * Récupération des données d'une liste d'éléments selon des critères de requête.
     *
     * @param array $query_args Liste des arguments de requête.
     *
     * @return array|object|OrderItemListInterface|OrderItemTypeInterface[]
     */
    public function getCollection($query_args = []);

    /**
     * Récupération d'un élément.
     *
     * @param int|object $id Identifiant de qualification de l'objet|Instance de l'objet.
     *
     * @return null|object|OrderItemTypeInterface
     */
    public function getItem($id = null);

    /**
     * Requête de récupération d'une liste d'élément associés à une commande
     *
     * @param array $query_args Liste des arguments de requête.
     *
     * @return array|OrderItemInterface[]
     */
    public function query($query_args = []);
}