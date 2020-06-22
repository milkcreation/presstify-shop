<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Wordpress\Contracts\Query\QueryPost;
use tiFy\Support\DateTime;

interface Order extends QueryPost, ShopAwareTrait
{
    /**
     * Récupération d'une instance basée sur une clé d'identification de commande.
     *
     * @param string $orderKey
     *
     * @return static|null
     */
    public static function createFromOrderKey(string $orderKey): ?QueryPost;

    /**
     * Ajout d'une note à la commande.
     *
     * @param string $note Message de la note.
     * @param boolean $is_customer Définie si la note est à destination du client.
     * @param boolean $by_user Lorsque la note provient de l'utilisateur.
     *
     * @return int
     */
    public function addNote(string $note, bool $is_customer = false, bool $by_user = false): int;

    /**
     * Ajout d'une ligne d'élément associé à la commande.
     *
     * @param OrderItem $item Instance de l'élément associé.
     *
     * @return static
     */
    public function addOrderItem(OrderItem $item): Order;

    /**
     * Création d'une ligne non typée.
     *
     * @param array $args
     *
     * @return OrderItem|object|null
     */
    public function createItem(array $args = []): ?OrderItem;

    /**
     * Création d'une ligne de coupon de réduction.
     *
     * @param array $args
     *
     * @return OrderItemCoupon|object|null
     */
    public function createItemCoupon(array $args = []): ?OrderItemCoupon;

    /**
     * Création d'une ligne de promotion.
     *
     * @param array $args
     *
     * @return OrderItemFee|object|null
     */
    public function createItemFee(array $args = []): ?OrderItemFee;

    /**
     * Création d'une ligne de produit.
     *
     * @param array $args
     *
     * @return OrderItemProduct|object|null
     */
    public function createItemProduct(array $args = []): ?OrderItemProduct;

    /**
     * Création d'une ligne de livraison.
     *
     * @param array $args
     *
     * @return OrderItemShipping|object|null
     */
    public function createItemShipping(array $args = []): ?OrderItemShipping;

    /**
     * Création d'une ligne de taxe.
     *
     * @param array $args
     *
     * @return OrderItemTax|object|null
     */
    public function createItemTax(array $args = []): ?OrderItemTax;

    /**
     * Récupération de la liste des attributs de l'addresse de facturation|Un attribut particulier.
     *
     * @param string|null $key Clé d'indice de l'attribut (syntaxe à point permise. null pour tous.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getBilling(?string $key = null, $default = null);

    /**
     * Récupération de l'url vers la page de paiement reçu.
     * @internal Lorsque le paiement a été accepté.
     *
     * @return string
     */
    public function getCheckoutOrderReceivedUrl(): string;

    /**
     * Récupération de l'url vers la page d'invitation au paiement de la commande.
     *
     * @return string
     */
    public function getCheckoutPaymentUrl(): string;

    /**
     * Récupération du client associé à la commande.
     *
     * @return UserCustomer
     */
    public function getCustomer(): UserCustomer;

    /**
     * Récupération de l'identifiant de qualification du client associé à la commande.
     *
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * Récupération du montant total de la remise.
     *
     * @return float
     */
    public function getDiscountTotal(): float;

    /**
     * Récupération des l'instance d'éléments associés à la commande.
     *
     * @param string|null $type Type d'éléments à retourner. coupon|fee|line_item|shipping|tax.
     *
     * @return OrderItem[]|array
     */
    public function getOrderItems(?string $type = null): array;

    /**
     * Récupération de la clé d'identification de la commande.
     *
     * @return string
     */
    public function getOrderKey(): string;

    /**
     * Récupération de la date de réglement.
     *
     * @return DateTime|null
     */
    public function getPaidDatetime(): ?DateTime;

    /**
     * Récupération de la méthode de paiement.
     *
     * @return string
     */
    public function getPaymentMethod(): string;

    /**
     * Récupération du l'intitulé de qualification de la méthode de paiement.
     *
     * @return string
     */
    public function getPaymentMethodLabel(): string;

    /**
     * Récupération de la liste des attributs de l'addresse de livraison|Un attribut particulier.
     *
     * @param string|null $key Clé d'indice de l'attribut (syntaxe à point permise. null pour tous.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getShipping(?string $key = null, $default = null);

    /**
     * Récupération du montant total de la livraison.
     *
     * @return float
     */
    public function getShippingTotal(): float;

    /**
     * Récupération du prix total cumulé.
     *
     * @return float
     */
    public function getSubtotal(): float;

    /**
     * Récupération du nom de qualification court du statut.
     *
     * @return string
     */
    public function getShortStatus(): string;

    /**
     * Récupération de l'intitulé de qualification du statut de commande.
     *
     * @return string
     */
    public function getStatusLabel(): string;

    /**
     * Récupération de l'identifiant de qualification de transaction.
     *
     * @return int|string
     */
    public function getTransactionId();

    /**
     * Récupération du montant total de la commande.
     *
     * @return float
     */
    public function getTotal(): float;

    /**
     * Vérification de correspondance du statut de la commande.
     *
     * @param string|array $status Statut unique ou liste de statuts de contrôle de correspondance.
     *
     * @return boolean
     */
    public function hasStatus($status): bool;

    /**
     * Vérifie de correspondance de l'identifiant de qualification d'un utilisateur avec celui associé à la commande.
     *
     * @param int $id Identifiant de qualification du client à vérifier.
     *
     * @return boolean
     */
    public function isCustomer(int $id): bool;

    /**
     * Cartographie des metadonnés d'attributs de la commande.
     *
     * @param string|array $key Clé d'indice|Tableau de cartographie attr <> meta
     * @param string|null $metaKey Clé d'indice de la métadonnée en correspondance.
     *
     * @return static
     */
    public function mapMeta($key, ?string $metaKey = null): Order;

    /**
     * Vérifie si une commande nécessite un paiement.
     *
     * @return boolean
     */
    public function needPayment(): bool;

    /**
     * Vérifie si une commande nécessite une intervention avant d'être complétée.
     * @internal Seul les produits téléchargeable et dématerialisé ne nécessite aucune intervention.
     *
     * @return boolean
     */
    public function needProcessing(): bool;

    /**
     * Action appelée à l'issue du processus de paiement.
     *
     * @param string $transaction_id Optional Identifiant de qualification de la transaction.
     *
     * @return boolean
     */
    public function paymentComplete($transaction_id = ''): bool;

    /**
     * Nombre de produit contenu dans la commande.
     *
     * @return int
     */
    public function productCount(): int;

    /**
     * Nombre total d'articles commandé.
     * @internal Calcul le cumul des quantités produits.
     *
     * @return int
     */
    public function quantityProductCount(): int;

    /**
     * Suppression de la liste des éléments de la commande.
     *
     * @param string $type Type d'élément de la commande à supprimer. Défaut null, pour tous.
     *
     * @return void
     */
    public function removeOrderItems(?string $type = null): void;

    /**
     * Sauvegarde de la liste des éléments.
     *
     * @return void
     */
    public function saveItems(): void;

    /**
     * Enregistrement de la liste des métadonnées déclarées.
     *
     * @return void
     */
    public function saveMetas(): void;

    /**
     * Définition d'un attribut de l'adresse de facturation.
     *
     * @param string $key Identifiant de qualification de l'attribut.
     * @param mixed $value Valeur de définition de l'attribut.
     *
     * @return static
     */
    public function setBilling(string $key, $value): Order;

    /**
     * Définition d'un attribut de l'adresse de livraison.
     *
     * @param string $key Identifiant de qualification de l'attribut.
     * @param mixed $value Valeur de définition de l'attribut.
     *
     * @return static
     */
    public function setShipping(string $key, $value): Order;

    /**
     * Mise à jour des données de commande.
     *
     * @return void
     */
    public function update(): void;

    /**
     * Mise à jour du statut et enregistrement immédiat.
     *
     * @param string $new_status Nom de qualification du nouveau statut.
     *
     * @return boolean
     */
    public function updateStatus(string $new_status): bool;
}