<?php

/**
 * @name \tiFy\Plugins\Shop\Orders\OrderItems\AbstractOrderItemType
 * @desc Controleur d'un élément associé à une commande.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Shop\Contracts\OrderInterface;
use tiFy\Plugins\Shop\Contracts\OrderItemInterface;
use tiFy\Plugins\Shop\Contracts\OrderItemTypeInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

abstract class AbstractOrderItemType extends ParamsBag implements OrderItemTypeInterface
{
    use ShopResolverTrait;

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
     * @param int|OrderItemInterface $item Identifiant de qualification
     * ou Objet de données de l'élément enregistré en base.
     * @param OrderInterface $order Instance de la commande associée.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct($item = 0, OrderInterface $order, Shop $shop)
    {
        $this->shop = $shop;

        parent::__construct($this->attributes);

        if ($item instanceof OrderItemInterface) :
            $this->setDatas($item);
            $this->setMetas($item);
        elseif (is_numeric($item) && $item > 0) :
            $item = app('shop.orders.order_items', [$this->order, $this->shop])->get($item);
            $this->setDatas($item);
            $this->setMetas($item);
        else :
            $this->set('order_id', $order->getId());
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return (int)$this->get('id', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return (string)$this->get('name', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return (int)$this->get('order_id', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return (string)$this->get('type', '');
    }

    /**
     * {@inheritdoc}
     */
    public function isType($type)
    {
        return $type === $this->getType();
    }

    /**
     * {@inheritdoc}
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

        return $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function saveMetas()
    {
        if (!$this->metas_map) :
            return;
        endif;

        foreach ($this->metas_map as $attr_key => $meta_key) :
            $this->saveMeta($meta_key, $this->get($attr_key), true);
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function saveMeta($meta_key, $meta_value, $unique = true)
    {
        if (!$id = $this->getId()) :
            return 0;
        endif;

        $db = $this->orders()->getDb();

        return $db->meta()->add($id, $meta_key, $meta_value);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDatas(OrderItemInterface $item)
    {
        foreach ($this->datas_map as $key => $data_key) :
            $this->set($key, $item->get($data_key));
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetas(OrderItemInterface $item)
    {
        foreach ($this->metas_map as $key => $meta_key) :
            $this->set($key, $item->getMeta($meta_key));
        endforeach;
    }
}