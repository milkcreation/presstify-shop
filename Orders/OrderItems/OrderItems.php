<?php

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Contracts\OrderInterface;
use tiFy\Plugins\Shop\Contracts\OrderItemInterface;
use tiFy\Plugins\Shop\Contracts\OrderItemsInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class OrderItems implements OrderItemsInterface
{
    use ShopResolverTrait;

    /**
     * Classe de rappel de la commande associée.
     * @var OrderInterface
     */
    protected $order;

    /**
     * CONSTRUCTEUR.
     *
     * @param OrderInterface $order Classe de rappel de la commande associée.
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    public function __construct(OrderInterface $order, Shop $shop)
    {
        $this->order = $order;
        $this->shop = $shop;
    }

    /**
     * @inheritdoc
     */
    public function delete(?string $type = null)
    {
        $db = $this->orders()->getDb();
        $where = ['order_id' => $this->order->getId()];
        if (!is_null($type)) {
            $where += ['order_item_type' => $type];
        }
        if ($ids = $db->select()->col_ids($where)) {
            foreach($ids as $id) {
                $db->meta()->deleteAll($id);
                $db->handle()->delete($where);
            }
        };
    }

    /**
     * @inheritdoc
     */
    public function getCollection($query_args = [])
    {
        if($items = $this->query($query_args)) {
            $items = array_map([$this, 'getItem'], $items);
        }
        return app('shop.orders.order_item_list', [$items, $this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id = null)
    {
        if ($id instanceof OrderItemInterface) {
            $item = $id;
        } elseif ($item = $this->query(
                [
                    'order_item_id' => $id,
                    'order_id' => $this->order->getId()
                ]
            )
        ) {
            $item = current($item);
        } else {
            return null;
        }

        switch($item->getType()) {
            case 'line_item' :
                return app('shop.orders.order_item_type_product', [$item, $this->order, $this->shop]);
                break;
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function query($query_args = [])
    {
        $query_args['order_id'] = $this->order->getId();

        if (!$items = $this->orders()->getDb()->select()->rows($query_args, ARRAY_A)) {
            return [];
        } else {
            return array_map(function (array $attrs) {
                return app('shop.orders.order_item', [$attrs, $this->shop]);
            }, $items);
        }
    }
}
