<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Session\Store;

/**
 * @mixin Store
 */
interface Session extends ShopAwareTrait
{
}