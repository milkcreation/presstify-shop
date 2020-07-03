<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\{Order, OrdersCollection as OrdersCollectionContract};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Collection;

class OrdersCollection extends Collection implements OrdersCollectionContract
{
    use ShopAwareTrait;

    /**
     * Liste des éléments déclarés.
     * @var Order[]
     */
    protected $items = [];

    /**
     * {@inheritDoc}
     *
     * @param Order $item
     *
     * @return Order
     */
    public function walk($item, $key = null): ?Order
    {
        if ($item instanceof Order) {
            return $this->items[$key] = $item;
        } else {
            return null;
        }
    }
}