<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use WP_Post;

interface Orders extends ShopAwareTrait
{
    /**
     * Récupération d'une collection de commande.
     *
     * @param Order[]|array $orders
     *
     * @return OrdersCollection
     */
    public function collect(array $orders = []): OrdersCollection;

    /**
     * Création d'une nouvelle commande.
     *
     * @return Order|null
     */
    public function create(): ?Order;

    /**
     * Récupération d'un élément.
     *
     * @param string|int|WP_Post|null $id Nom de qualification du post WP (slug, post_name)|
     * Identifiant de qualification du post WP|Object post WP|Post WP  de la page courante
     *
     * @return Order|null
     */
    public function get($id = null): ?Order;

    /**
     * Récupération du statut de commande par défaut.
     *
     * @return string
     */
    public function getDefaultStatus(): string;

    /**
     * Récupération de la liste des statuts nécessitant un paiement.
     *
     * @return array
     */
    public function getNeedPaymentStatuses(): array;

    /**
     * Récupération de la liste des statuts nécessitant un paiement.
     *
     * @return array
     */
    public function getNotEmptyCartStatuses(): array;

    /**
     * Récupération de la liste des statuts relatif à un processus de paiement abouti.
     *
     * @return array
     */
    public function getPaymentCompleteStatuses(): array;

    /**
     * Récupération de la liste des statuts relatif à un processus de paiement valide.
     *
     * @return array
     */
    public function getPaymentValidStatuses(): array;

    /**
     * Récupération de la liste des statuts en de commande déclarés.
     *
     * @return string[]
     */
    public function getRegisteredStatuses(): array;

    /**
     * Récupération de la liste des status.
     *
     * @return array
     */
    public function getStatuses(): array;

    /**
     * Récupération de l'intitulé de désignation d'un status.
     *
     * @param string $name Nom de qualification du status.
     * order-pending|order-processing|order-on-hold|order-completed|order-cancelled|order-refunded|order-failed.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function getStatusLabel(string $name, string $default = ''): string;

    /**
     * Controleur de traitement à l'issue du paiement.
     *
     * @param int $order_id
     *
     * @return mixed
     */
    public function handlePaymentComplete(int $order_id);

    /**
     * Vérifie si un statut correspond aux statuts de commandes.
     *
     * @param string $status Identifiant de qualification du statut à contrôler.
     *
     * @return boolean
     */
    public function hasStatus(string $status): bool;

    /**
     * Vérifie d'intégrité d'une commande.
     *
     * @param mixed $order
     *
     * @return boolean
     */
    public function is($order): bool;

    /**
     * Récupération des données d'une liste d'élément selon des critères de requête.
     *
     * @param array $args Liste des arguments de requête
     *
     * @return Order[]|array
     */
    public function query(array $args = []): array;
}