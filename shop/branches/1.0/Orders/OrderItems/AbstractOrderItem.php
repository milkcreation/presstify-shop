<?php

/**
 * @name AbstractOrderItem
 * @desc Controleur d'un élément associé à une commande
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
use tiFy\Plugins\Shop\Orders\OrderInterface;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

abstract class AbstractOrderItem extends Fluent implements OrderItemInterface, ProvideTraitsInterface
{
    use ProvideTraits;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel de la commande associée
     * @var OrderInterface
     */
    protected $order;

    /**
     * Identifiant de qualification de la donnée en base de données
     * @var int
     */
    protected $id = 0;

    /**
     * Liste des attributs.
     * @var array
     */
    protected $attributes = [
        'order_id' => 0,
        'name'     => ''
    ];

    /**
     * Cartographie des attributs en correspondance avec les métadonnées enregistrées en base.
     * @var array
     */
    protected $metas_map = [];

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique.
     * @param OrderInterface $order Classe de rappel de la commande associée.
     *
     * @return void
     */
    public function __construct(Shop $shop, OrderInterface $order)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Définition de la classe de rappel de la commande associée
        $this->order = $order;

        parent::__construct($this->attributes);
    }

    /**
     * Définition de la valeur d'un attribut de l'élément.
     *
     * @param string $key Identifiant de qualification de l'attribut
     * @param mixed $value Valeur de définition de l'attribut
     *
     * @return self
     */
    public function set($key, $value)
    {
        $this[$key] = $value;

        return $this;
    }

    /**
     * Récupération de la liste des attributs
     *
     * @return array
     */
    public function all()
    {
        return $this->getAttributes();
    }

    /**
     * Enregistrement de l'élément.
     *
     * @return int
     */
    public function save()
    {
        $db = $this->orders()->getDb();

        if (
            $this->id = $db->handle()->record(
                [
                    'order_item_id' => $this->id,
                    'order_item_name' => $this->getName(),
                    'order_item_type' => $this->getType(),
                    'order_id' => $this->getOrderId()
                ]
            )
        ) :
            $this->saveMetas();
        endif;

        return $this->id;
    }

    /**
     * Enregistrement de la liste des métadonnées déclarées.
     *
     * @return void
     */
    public function saveMetas()
    {
        if (!$this->metas_map) :
            return;
        endif;

        foreach($this->metas_map as $attr_key => $meta_key) :
            $this->addMeta($meta_key, $this->get($attr_key, ''), true);
        endforeach;
    }

    /**
     * Sauvegarde d'une métadonnée
     *
     * @param string $meta_key Clé d'identification de la métadonnée.
     * @param mixed $meta_value Valeur de la métadonnée
     * @param bool $unique Enregistrement unique d'une valeur pour la clé d'identification fournie.
     *
     * @return int Valeur de la clé primaire sauvegardée
     */
    public function addMeta($meta_key, $meta_value, $unique = true)
    {
        if (! $this->id) :
            return 0;
        endif;

        $db = $this->orders()->getDb();

        return $db->meta()->add($this->id, $meta_key, $meta_value);
    }

    /**
     * Récupération de la classe de rappel de la commande associée.
     *
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Récupération de l'identifiant de qualification de la commande.
     *
     * @return int
     */
    public function getOrderId()
    {
        return (int)$this->getOrder()->getId();
    }

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getName()
    {
        return (string)$this->get('name', '');
    }

    /**
     * Vérification de validité du type d'élement.
     *
     * @param string $type
     *
     * @return boolean
     */
    public function isType($type)
    {
        return $type === $this->getType();
    }
}