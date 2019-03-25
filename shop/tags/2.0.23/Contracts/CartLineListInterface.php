<?php

namespace tiFy\Plugins\Shop\Contracts;

use Illuminate\Support\Collection;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * Interface CartLineListInterface
 *
 * @package tiFy\Plugins\Shop\Contracts
 *
 * @mixin Collection
 */
interface CartLineListInterface extends ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{

}