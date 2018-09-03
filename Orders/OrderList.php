<?php

/**
 * @name OrderList
 * @desc Controleur de liste des commandes.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders;

use tiFy\PostType\Query\PostQueryCollection;
use tiFy\Plugins\Shop\Contracts\OrderListInterface;

class OrderList extends PostQueryCollection implements OrderListInterface
{

}