<?php

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeShippingInterface;

/**
 * Class OrderItemTypeShipping
 *
 * @desc Controleur de la livraison associée aux articles d'une commande.
 */
class OrderItemTypeShipping extends AbstractOrderItemType implements OrderItemTypeShippingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'shipping';
    }
}