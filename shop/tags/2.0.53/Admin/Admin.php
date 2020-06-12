<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Admin;

use tiFy\Plugins\Shop\Contracts\Admin as AdminContract;
use tiFy\Plugins\Shop\ShopAwareTrait;

class Admin implements AdminContract
{
    use ShopAwareTrait;
}