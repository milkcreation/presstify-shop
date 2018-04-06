<?php

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Core\Query\Controller\PostItemInterface;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeInterface;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeCouponInterface;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeFeeInterface;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeProductInterface;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeShippingInterface;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeTaxInterface;

interface OrderInterface extends PostItemInterface
{
    /**
     *
     */
    public function create();

    /**
     * Récupération de la liste des attributs.
     * @return array
     */
    public function all();

    /**
     * Définition d'un attribut.
     * @param string $key Identifiant de qualification déclaré.
     * @param mixed $value Valeur de définition de l'attribut.
     * @return mixed
     */
    public function set($key, $value);

    /**
     * Définition d'un attribut de l'adresse de facturation.
     * @param string $key Identifiant de qualification de l'attribut.
     * @param mixed $value Valeur de définition de l'attribut.
     * @return mixed
     */
    public function setBillingAttr($key, $value);

    /**
     * Définition d'un attribut de l'adresse de livraison.
     * @param string $key Identifiant de qualification de l'attribut.
     * @param mixed $value Valeur de définition de l'attribut.
     * @return mixed
     */
    public function setShippingAttr($key, $value);

    /**
     * Récupération du statut de publication.
     * @return string
     */
    public function getStatus();

    /**
     * Récupération de la valeur brute ou formatée de l'extrait.
     * @param bool $raw Formatage de la valeur
     * @return string
     */
    public function getExcerpt($raw = false);

    /**
     * Récupération de la clé d'identification de la commande
     * @return string
     */
    public function getOrderKey();

    /**
     * Récupération de l'identifiant de qualification du client associé à la commande.
     * @return int
     */
    public function getCustomerId();

    /**
     * Récupération d'un attribut pour un type d'adresse.
     * @param string $key Clé d'identification de l'attribut à retourner.
     * @param string $type Type d'adresse. billing|shipping.
     * @param mixed $default Valeur de retour par défaut.
     * @return mixed
     */
    public function getAddressAttr($key, $type = 'billing', $default = '');

    /**
     * Récupération du montant total de la commande.
     * @return float
     */
    public function getTotal();

    /**
     * Vérification de correspondance du client associé à la commande.
     * @param int $customer_id Identifiant de qualification de l'utilisateur à contrôler.
     * @return bool
     */
    public function isCustomer($customer_id);

    /**
     * Nombre de produit contenu dans la commande.
     * @return int
     */
    public function productCount();

    /**
     * Nombre total d'articles commandé.
     * @internal Calcul le cumul des quantités produits.
     * @return int
     */
    public function quantityProductCount();

    /**
     * Récupération de la méthode de paiement.
     * @return string
     */
    public function getPaymentMethod();

    /**
     * Ajout d'une note à la commande.
     * @param string $note Message de la note.
     * @param bool $is_customer Définie si la note est à destination du client.
     * @param bool $by_user Lorsque la note provient de l'utilisateur.
     * @return int Comment ID.
     */
    public function addNote($note, $is_customer = false, $by_user = false);

    /**
     * Vérification de correspondance du statut de la commande.
     * @param string|array $status Statut unique ou liste de statuts de contrôle de correspondance.
     * @return bool
     */
    public function hasStatus($status);

    /**
     * Mise à jour du statut et enregistrement immédiat.
     * @return bool
     */
    public function updateStatus($new_status);

    /**
     * Action appelée à l'issue du processus de paiement.
     * @param string $transaction_id Optional Identifiant de qualification de la transaction.
     * @return bool
     */
    public function paymentComplete($transaction_id = '');

    /**
     * Création d'une ligne de coupon de réduction.
     * @return object|OrderItemTypeCouponInterface
     */
    public function createItemCoupon();

    /**
     * Création d'une ligne de promotion.
     * @return object|OrderItemTypeFeeInterface
     */
    public function createItemFee();

    /**
     * Création d'une ligne d'article.
     * @return object|OrderItemTypeProductInterface
     */
    public function createItemProduct();

    /**
     * Création d'une ligne de livraison.
     * @return object|OrderItemTypeShippingInterface
     */
    public function createItemShipping();

    /**
     * Création d'une ligne de taxe.
     * @return object|OrderItemTypeTaxInterface
     */
    public function createItemTax();

    /**
     * Ajout d'une ligne d'élément associé à la commande.
     * @param OrderItemTypeInterface $item
     * @return void
     */
    public function addItem($item);

    /**
     * Récupération de la liste des éléments associés à la commande.
     *
     * @param string $type Type d'éléments à récupérer. null pour tous par défaut|coupon|fee|line_item (product)|shipping|tax.
     *
     * @return array
     */
    public function getItems($type = null);

    /**
     * Sauvegarde de la commande.
     * @return void
     */
    public function save();

    /**
     * Enregistrement de la liste des métadonnées déclarées.
     * @return void
     */
    public function saveMetas();

    /**
     * Sauvegarde de la liste des éléments.
     * @return void
     */
    public function saveItems();

    /**
     * Vérifie si une commande nécessite un paiement.
     *
     * @return bool
     */
    public function needPayment();

    /**
     * Vérifie si une commande nécessite une intervention avant d'être complétée.
     *
     * @return bool
     */
    public function needProcessing();

    /**
     * Récupération de l'url vers la page d'invitation au paiement de la commande.
     * @return string
     */
    public function getCheckoutPaymentUrl();

    /**
     * Récupération de l'url ver la page de remerciement.
     * @internal Lorsque le paiement a été accepté.
     * @return string
     */
    public function getCheckoutOrderReceivedUrl();
}