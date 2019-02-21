<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\User\SessionStore;

/**
 * Interface SessionInterface
 * @package tiFy\Plugins\Shop\Contracts
 *
 * @mixin SessionStore
 */
interface SessionInterface extends BootableControllerInterface, ShopResolverInterface
{

}