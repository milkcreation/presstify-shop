<?php

/**
 * @name \tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeShipping
 * @desc Controleur de la livraison associée aux articles d'une commande.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeShippingInterface;

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