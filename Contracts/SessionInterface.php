<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\User\SessionStore;

/**
 * @mixin SessionStore
 */
interface SessionInterface extends BootableControllerInterface, ShopResolverInterface
{

}