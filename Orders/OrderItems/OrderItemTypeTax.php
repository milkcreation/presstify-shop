<?php

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeTaxInterface;

/**
 * Class OrderItemTypeTax
 *
 * @desc Controleur de la taxe associée à une commande.
 */
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