<?php

namespace tiFy\Plugins\Shop\Contracts;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;

interface CartLineListInterface extends ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{

}