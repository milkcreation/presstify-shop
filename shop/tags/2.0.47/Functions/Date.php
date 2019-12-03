<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Plugins\Shop\Contracts\{FunctionsDate as FunctionsDateContract};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\DateTime;

class Date extends DateTime implements FunctionsDateContract
{
    use ShopAwareTrait;
}