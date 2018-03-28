<?php

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Core\Query\Controller\PostItemInterface;
use tiFy\Plugins\Shop\Products\ProductItemInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemCouponInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemFeeInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemProductInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemShippingInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemTaxInterface;

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
     * Vérification de correspondance du statut de la commande.
     * @param string|array $status Statut unique ou liste de statuts de contrôle de correspondance.
     * @return bool
     */
    public function hasStatus($status);

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
     * Action appelée à l'issue du processus de paiement.
     * @param string $transaction_id Optional Identifiant de qualification de la transaction.
     * @return bool
     */
    public function paymentComplete($transaction_id = '');

    /**
     * Création d'une ligne de coupon de réduction.
     * @return object|OrderItemCouponInterface
     */
    public function createItemCoupon();

    /**
     * Création d'une ligne de promotion.
     * @return object|OrderItemFeeInterface
     */
    public function createItemFee();

    /**
     * Création d'une ligne d'article.
     * @return object|OrderItemProductInterface
     */
    public function createItemProduct(ProductItemInterface $product);

    /**
     * Création d'une ligne de livraison.
     * @return object|OrderItemShippingInterface
     */
    public function createItemShipping();

    /**
     * Création d'une ligne de taxe.
     * @return object|OrderItemTaxInterface
     */
    public function createItemTax();

    /**
     * Ajout d'une ligne d'élément associé à la commande.
     * @param OrderItemInterface $item
     * @return void
     */
    public function addItem($item);

    /**
     * Récupération de la liste des éléments associés à la commande.
     * @return array
     */
    public function getItems();

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