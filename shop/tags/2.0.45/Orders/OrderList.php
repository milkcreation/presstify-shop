<?php

namespace tiFy\Plugins\Shop\Orders;

use tiFy\PostType\Query\PostQueryCollection;
use tiFy\Plugins\Shop\Contracts\OrderInterface;
use tiFy\Plugins\Shop\Contracts\OrderListInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;
use WP_Query;

/**
 * Class OrderList
 *
 * @desc Controleur de liste des commandes.
 */
class OrderList extends PostQueryCollection implements OrderListInterface
{
    use ShopResolverTrait;

    /**
     * Liste des éléments déclarés.
     * @var OrderInterface[]
     */
    protected $items = [];

    /**
     * Nombre d'élément total trouvés
     * @var int
     */
    protected $total = 0;

    /**
     * CONSTRUCTEUR.
     *
     * @param array|WP_Query|OrderInterface[] $items Liste des éléments déclarés.
     *
     * @return void
     */
    public function __construct($items = [], Shop $shop)
    {
        $this->shop = $shop;
        if ($items instanceof WP_Query) {
            $this->setTotal($items->found_posts);
        }

        parent::__construct($items);
    }

    /**
     * Récupération du nombre d'enregistrement total.
     *
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Définition du nombre d'enregistrement total.
     *
     * @param int $total
     *
     * @return $this
     */
    public function setTotal($total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return OrderInterface
     */
    public function wrap($post, $key = null)
    {
        return $this->items[$key] = app('shop.orders.order', [$post, $this->shop]);
    }
}