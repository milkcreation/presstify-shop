<?php declare(strict_types=1);

namespace __tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\{OrderItemsCollection as OrderItemsCollectionContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Collection;

class OrderItemsCollection extends Collection implements OrderItemsCollectionContract
{
    use ShopAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);
    }

    /**
     * Requête de récupération de la liste des éléments.
     *
     * @param array ...$args Liste des arguments dynamiques passés à la méthode.
     *
     * @return static
     */
    public function query(...$args): OrderItemsCollectionContract
    {
        return $this;
    }
}