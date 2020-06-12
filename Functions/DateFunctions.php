<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Plugins\Shop\Contracts\{DateFunctions as DateFunctionsContract};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\DateTime;

class DateFunctions extends DateTime implements DateFunctionsContract
{
    use ShopAwareTrait;
}