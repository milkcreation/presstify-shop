<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use Illuminate\Support\Collection;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * @mixin Collection
 */
interface CartLinesCollection extends ArrayAccess, Countable, IteratorAggregate, JsonSerializable, ShopAwareTrait
{
    /**
     * Réinitialisation de la liste des éléments.
     *
     * @return static
     */
    public function flush(): CartLinesCollection;
}