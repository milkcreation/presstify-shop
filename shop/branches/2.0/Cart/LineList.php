<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Collection;
use tiFy\Plugins\Shop\Contracts\{CartLineInterface as CartLineContract, CartLineListInterface as CartLineListContract};
use tiFy\Plugins\Shop\ShopAwareTrait;

class LineList extends Collection implements CartLineListContract
{
    use ShopAwareTrait;

    /**
     * Liste des élements.
     * @var CartLineContract[]
     */
    protected $items = [];

    /**
     * @inheritDoc
     */
    public function flush(): CartLineListContract
    {
        $this->items = [];

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return CartLineContract
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }
}