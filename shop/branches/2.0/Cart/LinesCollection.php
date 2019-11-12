<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Collection;
use tiFy\Plugins\Shop\Contracts\{CartLine, CartLinesCollection as CartLinesCollectionContract};
use tiFy\Plugins\Shop\ShopAwareTrait;

class LinesCollection extends Collection implements CartLinesCollectionContract
{
    use ShopAwareTrait;

    /**
     * Liste des élements.
     * @var CartLine[]
     */
    protected $items = [];

    /**
     * @inheritDoc
     */
    public function flush(): CartLinesCollectionContract
    {
        $this->items = [];

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return CartLine
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }
}