<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use Illuminate\Support\Collection;
use tiFy\Wordpress\Contracts\Query\QueryPost;
use tiFy\Support\DateTime;

interface Order extends QueryPost
{
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
     * @param OrderItemType $item
     *
     * @return void
     */
    public function addItem(OrderItemType $item): void;

    /**
     * Création d'une ligne de coupon de réduction.
     *
     * @return OrderItemTypeCoupon|object|null
     */
    public function createItemCoupon(): ?OrderItemTypeCoupon;

    /**
     * Création d'une ligne de promotion.
     *
     * @return OrderItemTypeFee|object|null
     */
    public function createItemFee(): ?OrderItemTypeFee;

    /**
     * Création d'une ligne de produit.
     *
     * @return OrderItemTypeProduct|object|null
     */
    public function createItemProduct(): ?OrderItemTypeProduct;

    /**
     * Création d'une ligne de livraison.
     *
     * @return OrderItemTypeShipping|object|null
     */
    public function createItemShipping(): ?OrderItemTypeShipping;

    /**
     * Création d'une ligne de taxe.
     *
     * @return OrderItemTypeTax|object|null
     */
    public function createItemTax(): ?OrderItemTypeTax;

    /**
     * Récupération d'un attribut pour un type d'adresse.
     *
     * @param string $key Clé d'identification de l'attribut à retourner.
     * @param string $type Type d'adresse. billing|shipping.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getAddressAttr($key, $type = 'billing', $default = '');

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
     * Récupération de la liste des éléments associés à la commande.
     *
     * @param string $type Type d'éléments à récupérer.
     * null pour tous par défaut|coupon|fee|line_item (product)|shipping|tax.
     *
     * @return Collection
     */
    public function getItems($type = null);

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
     * Récupération du nom de qualification court du statut.
     *
     * @return string
     */
    public function getShortStatus(): string;

    /**
     *
     */
    public function getStatusLabel();

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
     * Vérification de correspondance du client associé à la commande.
     *
     * @param int $customer_id Identifiant de qualification de l'utilisateur à contrôler.
     *
     * @return boolean
     */
    public function isCustomer($customer_id): bool;

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
     * Récupération de la liste des attributs.
     *
     * @return void
     */
    public function read(): void;

    /**
     * Suppression de la liste des éléments de la commande.
     *
     * @param string $type Type d'élément de la commande à supprimer. Défaut null, pour tous.
     *
     * @return void
     */
    public function removeItems(?string $type = null): void;

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
     * @return mixed
     */
    public function setBillingAttr($key, $value);

    /**
     * Définition d'un attribut de l'adresse de livraison.
     *
     * @param string $key Identifiant de qualification de l'attribut.
     * @param mixed $value Valeur de définition de l'attribut.
     *
     * @return mixed
     */
    public function setShippingAttr($key, $value);

    /**
     * Mise à jour du statut et enregistrement immédiat.
     *
     * @param string $new_status Nom de qualification du nouveau statut.
     *
     * @return boolean
     */
    public function updateStatus($new_status): bool;
}