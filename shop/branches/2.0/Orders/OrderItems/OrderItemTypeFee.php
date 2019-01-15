<?php

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeFeeInterface;

/**
 * Class OrderItemTypeFee
 *
 * @desc Controleur d'une ligne d'article associée à une commande.
 */
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