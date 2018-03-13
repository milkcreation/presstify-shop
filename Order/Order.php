<?php

/**
 * @name Order
 * @desc Gestion de commande
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Cart
 * @version 1.1
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Order;

use tiFy\App\Traits\App as TraitsApp;

class Order implements OrderInterface
{
    use TraitsApp;
}