<?php

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Core\Db\Factory as DbFactory;
use tiFy\Plugins\Shop\Shop;

interface OrdersInterface
{
    /**
     * Instanciation de la classe.
     * @param Shop $shop
     * @return Orders
     */
    public static function make(Shop $shop);

    /**
     * Récupération d'un élément.
     *
     * @param string|int|\WP_Post|null $id Nom de qualification du post WP (slug, post_name)|Identifiant de qualification du post WP|Object post WP|Post WP  de la page courante
     *
     * @return null|object|OrderInterface
     */
    public function get($id = null);

    /**
     * Récupération d'un élément selon un attribut particulier.
     *
     * @param string $key Identifiant de qualification de l'attribut. défaut name.
     * @param string $value Valeur de l'attribut
     *
     * @return null|object|OrderInterface
     */
    public function getBy($key = 'name', $value);

    /**
     * Récupération des données d'une liste d'élément selon des critères de requête.
     *
     * @param array $query_args Liste des arguments de requête
     *
     * @return array|OrderListInterface
     */
    public function getList($query_args = []);

    /**
     * Récupération du controleur de base de données.
     *
     * @return null|DbFactory
     */
    public function getDb();

    /**
     * Création d'une nouvelle commande.
     *
     * @return null|OrderInterface
     */
    public function create();

    /**
     * Vérifie d'intégrité d'une commande.
     *
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function is($order);

    /**
     * Récupération de la liste déclaration de statut de commande.
     *
     * @return array
     */
    public function getRegisteredStatuses();

    /**
     * Récupération de la liste des status.
     *
     * @return array
     */
    public function getStatuses();


    /**
     * Récupération de la liste des statuts en relation avec les post.
     *
     * @return string[]
     */
    public function getRelPostStatuses();

    /**
     * Récupération du statut de commande par défaut.
     *
     * @return string
     */
    public function getDefaultStatus();

    /**
     * Récupération de la liste des statuts nécessitant un paiement.
     *
     * @return array
     */
    public function getNeedPaymentStatuses();

    /**
     * Récupération de la liste des statuts relatif à un processus de paiement abouti.
     *
     * @return array
     */
    public function getPaymentCompleteStatuses();
}