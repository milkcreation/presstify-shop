<?php

/**
 * @name OrderItem
 * @desc Controleur d'un élément de commande en base de données.
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Orders\OrderItems
 * @version 1.1
 * @since 1.1
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use Illuminate\Support\Fluent;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;

class OrderItem extends Fluent implements ProvideTraitsInterface, OrderItemInterface
{
    use TraitsApp, ProvideTraits;

    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attributes {
     *      Liste des attributs de l'élément.
     *
     *      @var int $order_item_id
     *      @var string $order_item_name
     *      @var string $order_item_type
     *      @var string $order_id
     * }
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    public function __construct($attributes = [], Shop $shop)
    {
        // Définition de la classe de rappel de la boutique.
        $this->shop = $shop;

        parent::__construct($attributes);
    }

    /**
     * Récupération d'une metadonnée d'élement associé à la commande
     *
     * @param string $meta_key Clé d'index de la métadonnée à récupérer.
     * @param bool $single Type de récupération. single|multi.
     *
     * @return mixed
     */
    public function getMeta($meta_key, $single = true)
    {
        return $this->orders()->getDb()->meta()->get($this->getId(), $meta_key, $single);
    }

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @internal Identifiant de l'élément en base de données.
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->get('order_item_id', 0);
    }

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getName()
    {
        return (string)$this->get('order_item_name', '');
    }

    /**
     * Récupération du type d'élement associé à la commande
     *
     * @return string coupon|fee|line_item|shipping|tax
     */
    public function getType()
    {
        return (string)$this->get('order_item_type', '');
    }

    /**
     * Récupération de l'identifiant de qualification de la commande.
     *
     * @return int
     */
    public function getOrderId()
    {
        return (int)$this->get('order_id', 0);
    }
}