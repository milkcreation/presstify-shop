<?php

/**
 * @name \tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeTax
 * @desc Controleur de la taxe associée à une commande.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeTaxInterface;

class OrderItemTypeTax extends AbstractOrderItemType implements OrderItemTypeTaxInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'tax';
    }
}