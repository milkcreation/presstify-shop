<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\{Order as OrderContract, OrderItem as OrderItemContract, OrderItems as OrderItemsContract};
use tiFy\Plugins\Shop\ShopAwareTrait;

class OrderItems implements OrderItemsContract
{
    use ShopAwareTrait;

    /**
     * Instance de la commande associée.
     * @var OrderContract
     */
    protected $order;

    /**
     * CONSTRUCTEUR.
     *
     * @param OrderContract $order
     *
     * @return void
     */
    public function __construct(OrderContract $order)
    {
        $this->order = $order;

        $this->setShop($this->order->shop());
    }

    /**
     * @inheritDoc
     */
    public function delete(?string $type = null)
    {
        $db = $this->shop()->orders()->getDb();
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
     * @inheritDoc
     */
    public function get($id = null)
    {
        if ($id instanceof OrderItemContract) {
            $item = $id;
        } elseif ($item = $this->query([
            'order_item_id' => $id,
            'order_id' => $this->order->getId()
        ])) {
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
     * @inheritDoc
     * @todo IDENTIQUE A QUERY ??
     */
    public function getCollection($query_args = [])
    {
        if($items = $this->query($query_args)) {
            $items = array_map([$this, 'get'], $items);
        }
        return app('shop.orders.order_item_list', [$items, $this->shop]);
    }

    /**
     * @inheritDoc
     * @todo IDENTIQUE A GETCOLLECTION ??
     */
    public function query($query_args = [])
    {
        $query_args['order_id'] = $this->order->getId();

        if (!$items = $this->shop()->orders()->getDb()->select()->rows($query_args, ARRAY_A)) {
            return [];
        } else {
            return array_map(function (array $attrs) {
                return app('shop.orders.order_item', [$attrs, $this->shop]);
            }, $items);
        }
    }
}
