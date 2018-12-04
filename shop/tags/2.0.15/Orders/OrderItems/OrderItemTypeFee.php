<?php

/**
 * @name \tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeFee
 * @desc Controleur d'une ligne d'article associée à une commande.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeFeeInterface;

class OrderItemTypeFee extends  AbstractOrderItemType implements OrderItemTypeFeeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'fee';
    }
}