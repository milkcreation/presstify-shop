<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\PostType\PostQueryItem;

interface OrderInterface extends PostQueryItem
{
    /**
     * Ajout d'une note à la commande.
     *
     * @param string $note Message de la note.
     * @param bool $is_customer Définie si la note est à destination du client.
     * @param bool $by_user Lorsque la note provient de l'utilisateur.
     *
     * @return int
     */
    public function addNote($note, $is_customer = false, $by_user = false);

    /**
     * Ajout d'une ligne d'élément associé à la commande.
     *
     * @param OrderItemTypeInterface $item
     *
     * @return void
     */
    public function addItem($item);

    /**
     * Récupération de la liste des attributs.
     *
     * @return array
     */
    public function all();

    /**
     *
     */
    public function create();

    /**
     * Création d'une ligne de coupon de réduction.
     *
     * @return null|object|OrderItemTypeCouponInterface
     */
    public function createItemCoupon();

    /**
     * Création d'une ligne de promotion.
     *
     * @return null|object|OrderItemTypeFeeInterface
     */
    public function createItemFee();

    /**
     * Création d'une ligne de produit.
     *
     * @return null|object|OrderItemTypeProductInterface
     */
    public function createItemProduct();

    /**
     * Création d'une ligne de livraison.
     *
     * @return null|object|OrderItemTypeShippingInterface
     */
    public function createItemShipping();

    /**
     * Création d'une ligne de taxe.
     *
     * @return null|object|OrderItemTypeTaxInterface
     */
    public function createItemTax();

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
    public function getCheckoutOrderReceivedUrl();

    /**
     * Récupération de l'url vers la page d'invitation au paiement de la commande.
     *
     * @return string
     */
    public function getCheckoutPaymentUrl();

    /**
     * Récupération de l'identifiant de qualification du client associé à la commande.
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Récupération de la liste des éléments associés à la commande.
     *
     * @param string $type Type d'éléments à récupérer. null pour tous par défaut|coupon|fee|line_item (product)|shipping|tax.
     *
     * @return Collection
     */
    public function getItems($type = null);

    /**
     * Récupération de la clé d'identification de la commande.
     *
     * @return string
     */
    public function getOrderKey();

    /**
     * Récupération de la méthode de paiement.
     *
     * @return string
     */
    public function getPaymentMethod();

    /**
     * {@inheritdoc}
     */
    public function getStatus();

    /**
     * {@inheritdoc}
     */
    public function getStatusLabel();

    /**
     * Récupération du montant total de la commande.
     *
     * @return float
     */
    public function getTotal();

    /**
     * Vérification de correspondance du statut de la commande.
     *
     * @param string|array $status Statut unique ou liste de statuts de contrôle de correspondance.
     *
     * @return bool
     */
    public function hasStatus($status);

    /**
     * Vérification de correspondance du client associé à la commande.
     *
     * @param int $customer_id Identifiant de qualification de l'utilisateur à contrôler.
     *
     * @return bool
     */
    public function isCustomer($customer_id);

    /**
     * Vérifie si une commande nécessite un paiement.
     *
     * @return bool
     */
    public function needPayment();

    /**
     * Vérifie si une commande nécessite une intervention avant d'être complétée.
     * @internal Seul les produits téléchargeable et dématerialisé ne nécessite aucune intervention.
     *
     * @return bool
     */
    public function needProcessing();

    /**
     * Action appelée à l'issue du processus de paiement.
     *
     * @param string $transaction_id Optional Identifiant de qualification de la transaction.
     *
     * @return bool
     */
    public function paymentComplete($transaction_id = '');

    /**
     * Nombre de produit contenu dans la commande.
     *
     * @return int
     */
    public function productCount();

    /**
     * Nombre total d'articles commandé.
     * @internal Calcul le cumul des quantités produits.
     *
     * @return int
     */
    public function quantityProductCount();

    /**
     * Récupération de la liste des attributs.
     *
     * @return void
     */
    public function read();

    /**
     * Sauvegarde de la commande
     *
     * @return void
     */
    public function save();

    /**
     * Sauvegarde de la liste des éléments.
     *
     * @return void
     */
    public function saveItems();

    /**
     * Enregistrement de la liste des métadonnées déclarées.
     *
     * @return void
     */
    public function saveMetas();

    /**
     * Définition d'un attribut.
     *
     * @param string $key Identifiant de qualification déclaré.
     * @param mixed $value Valeur de définition de l'attribut.
     *
     * @return $this
     */
    public function set($key, $value);

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
     * @return bool
     */
    public function updateStatus($new_status);
}