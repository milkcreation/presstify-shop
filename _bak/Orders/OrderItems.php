<?php declare(strict_types=1);

namespace __tiFy\Plugins\Shop\Orders;

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
}
