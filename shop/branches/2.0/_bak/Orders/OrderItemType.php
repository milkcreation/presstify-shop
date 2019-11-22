<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\{Order, OrderItem, OrderItemType as OrderItemTypeContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\ParamsBag;

abstract class OrderItemType extends ParamsBag implements OrderItemTypeContract
{
    use ShopAwareTrait;

    /**
     * Liste des attributs.
     * @var array
     */
    protected $attributes = [
        'id'       => 0,
        'name'     => '',
        'type'     => '',
        'order_id' => 0,
    ];

    /**
     * Cartographie des attributs en correspondance avec les données enregistrées en base.
     * @var array
     */
    protected $datasMap = [
        'id'       => 'order_item_id',
        'name'     => 'order_item_name',
        'type'     => 'order_item_type',
        'order_id' => 'order_id',
    ];

    /**
     * Cartographie des attributs en correspondance avec les métadonnées enregistrées en base.
     * @var array
     */
    protected $metasMap = [];

    /**
     * Instance de la commande associée.
     * @var Order
     */
    protected $order;

    /**
     * CONSTRUCTEUR.
     *
     * @param int|OrderItem $item Identifiant de qualification
     * ou Objet de données de l'élément enregistré en base.
     * @param Order $order Instance de la commande associée.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct($item = 0, Order $order, Shop $shop)
    {
        $this->order = $order;
        $this->shop = $shop;

        //parent::__construct($this->attributes);

        if ($item instanceof OrderItem) {
            $this->setDatas($item);
            $this->setMetas($item);
        } elseif (is_numeric($item) && $item > 0) {
            $item = app('shop.orders.order_items', [$this->order, $this->shop])->get($item);
            $this->setDatas($item);
            $this->setMetas($item);
        } else {
            $this->set('order_id', $order->getId());
        }
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return (int)$this->get('id', 0);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return (string)$this->get('name', '');
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return (int)$this->get('order_id', 0);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return (string)$this->get('type', '');
    }

    /**
     * @inheritDoc
     */
    public function isType($type)
    {
        return $type === $this->getType();
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        $db = $this->shop()->orders()->getDb();

        if (
        $id = $db->handle()->record(
            [
                'order_item_id'   => $this->getId(),
                'order_item_name' => $this->getName(),
                'order_item_type' => $this->getType(),
                'order_id'        => $this->getOrderId(),
            ]
        )
        ) {
            $this->set('id', $id);
            $this->saveMetas();
        }
        return $this->getId();
    }

    /**
     * @inheritDoc
     */
    public function saveMetas()
    {
        if (!$this->metas_map) {
            return;
        }

        foreach ($this->metas_map as $attr_key => $meta_key) {
            $this->saveMeta($meta_key, $this->get($attr_key), true);
        }
    }

    /**
     * @inheritDoc
     */
    public function saveMeta($meta_key, $meta_value, $unique = true)
    {
        if (!$id = $this->getId()) {
            return 0;
        }

        return $this->shop()->orders()->getDb()->meta()->add($id, $meta_key, $meta_value);
    }

    /**
     * @inheritDoc
     */
    public function setDatas(OrderItem $item)
    {
        foreach ($this->datas_map as $key => $data_key) {
            $this->set($key, $item->get($data_key));
        }
    }

    /**
     * @inheritDoc
     */
    public function setMetas(OrderItem $item)
    {
        foreach ($this->metas_map as $key => $meta_key) {
            $this->set($key, $item->getMeta($meta_key));
        }
    }
}