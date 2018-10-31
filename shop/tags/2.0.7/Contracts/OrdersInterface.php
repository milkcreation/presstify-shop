<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Db\DbItemInterface;
use tiFy\Contracts\PostType\PostQuery;
use tiFy\Plugins\Shop\Contracts\BootableControllerInterface;
use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;

interface OrdersInterface extends BootableControllerInterface, PostQuery, ShopResolverInterface
{
    /**
     * Création d'une nouvelle commande.
     *
     * @return null|OrderInterface
     */
    public function create();

    /**
     * Récupération des données d'une liste d'élément selon des critères de requête.
     *
     * @param array $query_args Liste des arguments de requête
     *
     * @return array|OrderListInterface
     */
    public function getCollection($query_args = []);

    /**
     * Récupération du controleur de base de données.
     *
     * @return null|DbItemInterface
     */
    public function getDb();

    /**
     * Récupération du statut de commande par défaut.
     *
     * @return string
     */
    public function getDefaultStatus();

    /**
     * Récupération d'un élément.
     *
     * @param string|int|\WP_Post|null $id Nom de qualification du post WP (slug, post_name)|Identifiant de qualification du post WP|Object post WP|Post WP  de la page courante
     *
     * @return null|OrderInterface
     */
    public function getItem($id = null);

    /**
     * Récupération d'un élément selon un attribut particulier.
     *
     * @param string $key Identifiant de qualification de l'attribut. défaut name.
     * @param string $value Valeur de l'attribut
     *
     * @return null|OrderInterface
     */
    public function getItemBy($key = 'name', $value);

    /**
     * Récupération de la liste des statuts nécessitant un paiement.
     *
     * @return array
     */
    public function getNeedPaymentStatuses();

    /**
     * Récupération de la liste des statuts nécessitant un paiement.
     *
     * @return array
     */
    public function getNotEmptyCartStatus();

    /**
     * Récupération de la liste des statuts relatif à un processus de paiement abouti.
     *
     * @return array
     */
    public function getPaymentCompleteStatuses();

    /**
     * Récupération de la liste des statuts relatif à un processus de paiement valide.
     *
     * @return array
     */
    public function getPaymentValidStatuses();

    /**
     * Récupération de la liste déclaration de statut de commande.
     *
     * @return array
     */
    public function getRegisteredStatuses();

    /**
     * Récupération de la liste des statuts en relation avec les post.
     *
     * @return string[]
     */
    public function getRelPostStatuses();

    /**
     * Récupération de la liste des status.
     *
     * @return array
     */
    public function getStatuses();

    /**
     * Récupération de l'intitulé de désignation d'un status.
     *
     * @param string $name Nom de qualification du status. order-pending|order-processing|order-on-hold|order-completed|order-cancelled|order-refunded|order-failed.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return array
     */
    public function getStatusLabel($name, $default = '');

    /**
     * Vérifie d'intégrité d'une commande.
     *
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function is($order);

    /**
     * Vérifie si un statut correspond aux statuts de commandes.
     *
     * @param string $status Identifiant de qualification du statut à contrôler.
     *
     * @return bool
     */
    public function isStatus($status);
}