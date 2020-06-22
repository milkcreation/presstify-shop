<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Api;

use tiFy\Plugins\Shop\Contracts\Api as ApiContract;
use tiFy\Plugins\Shop\ShopAwareTrait;

class Api implements ApiContract
{
    use ShopAwareTrait;
}