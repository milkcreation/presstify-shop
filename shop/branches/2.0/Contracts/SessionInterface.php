<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\User\Session\StoreInterface;
use tiFy\Plugins\Shop\Contracts\BootableControllerInterface;
use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;

interface SessionInterface extends BootableControllerInterface, ShopResolverInterface
{

}