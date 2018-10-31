<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\User\SessionStore;
use tiFy\Plugins\Shop\Contracts\BootableControllerInterface;
use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;

/**
 * Interface SessionInterface
 * @package tiFy\Plugins\Shop\Contracts
 *
 * @mixin SessionStore
 */
interface SessionInterface extends BootableControllerInterface, ShopResolverInterface
{

}