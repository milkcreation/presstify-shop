<?php

/**
 * @name AbstractOrderItemType
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

abstract class AbstractOrderItemType extends Fluent implements OrderItemTypeInterface, ProvideTraitsInterface
{
    use ProvideTraits;

    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel de la commande associée.
     * @var OrderInterface
     */
    protected $order;

    /**
     * Cartographie des attributs en correspondance avec les données enregistrées en base.
     * @var array
     */
    protected $datas_map = [
        'id'       => 'order_item_id',
        'name'     => 'order_item_name',
        'type'     => 'order_item_type',
        'order_id' => 'order_id'
    ];

    /**
     * Cartographie des attributs en correspondance avec les métadonnées enregistrées en base.
     * @var array
     */
    protected $metas_map = [];

    /**
     * Liste des attributs.
     * @var array
     */
    protected $attributes = [
        'id'       => 0,
        'name'     => '',
        'type'     => '',
        'order_id' => 0
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param int|OrderItemInterface $item Identifiant de qualification|Objet de données de l'élément enregistré en base.
     * @param OrderInterface $order Classe de rappel de la commande associée.
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    public function __construct($item = 0, OrderInterface $order, Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        parent::__construct($this->attributes);

        if ($item instanceof OrderItem) :
            $this->setDatas($item);
            $this->setMetas($item);
        elseif (is_numeric($item) && $item > 0) :
            $item = $this->provide('orders.order_items', [$this->order, $this->shop])->get($item);
            $this->setDatas($item);
            $this->setMetas($item);
        else :
            $this->set('order_id', $order->getId());
        endif;
    }

    /**
     * Définition de la liste des données de l'élément enregistrées en base de données.
     *
     * @param OrderItemInterface $item Classe de rappel des données de l'élément en base.
     *
     * @return void
     */
    public function setDatas(OrderItemInterface $item)
    {
        foreach ($this->datas_map as $key => $data_key) :
            $this->set($key, $item->get($data_key));
        endforeach;
    }

    /**
     * Définition de la liste des metadonnées de l'élément enregistrées en base de données.
     *
     * @param OrderItemInterface $item Classe de rappel des données de l'élément en base.
     *
     * @return void
     */
    public function setMetas(OrderItemInterface $item)
    {
        foreach ($this->metas_map as $key => $meta_key) :
            $this->set($key, $item->getMeta($meta_key));
        endforeach;
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
     * Récupération de l'identifiant de qualification.
     * @internal Identifiant de l'élément en base de données.
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->get('id', 0);
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
     * Récupération du type d'élement associé à la commande.
     *
     * @return string
     */
    public function getType()
    {
        return (string)$this->get('type', '');
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

    /**
     * Enregistrement de l'élément.
     *
     * @return int
     */
    public function save()
    {
        $db = $this->orders()->getDb();

        if (
            $id = $db->handle()->record(
                [
                    'order_item_id'   => $this->getId(),
                    'order_item_name' => $this->getName(),
                    'order_item_type' => $this->getType(),
                    'order_id'        => $this->getOrderId()
                ]
            )
        ) :
            $this->set('id', $id);
            $this->saveMetas();
        endif;

        return $this->id;
    }

    /**
     * Enregistrement de la liste des métadonnées cartographiées.
     *
     * @return void
     */
    public function saveMetas()
    {
        if (!$this->metas_map) :
            return;
        endif;

        foreach ($this->metas_map as $attr_key => $meta_key) :
            $this->saveMeta($meta_key, $this->get($attr_key, ''), true);
        endforeach;
    }

    /**
     * Enregistrement d'une métadonnée
     *
     * @param string $meta_key Clé d'identification de la métadonnée.
     * @param mixed $meta_value Valeur de la métadonnée
     * @param bool $unique Enregistrement unique d'une valeur pour la clé d'identification fournie.
     *
     * @return int Valeur de la clé primaire de la métadonnée enregistrée.
     */
    public function saveMeta($meta_key, $meta_value, $unique = true)
    {
        if (!$this->id) :
            return 0;
        endif;

        $db = $this->orders()->getDb();

        return $db->meta()->add($this->id, $meta_key, $meta_value);
    }
}