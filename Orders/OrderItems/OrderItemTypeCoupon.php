<?php

/**
 * @name \tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeCoupon
 * @desc Controleur d'une ligne d'un coupon de réduction associé à une commande.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeCouponInterface;

class OrderItemTypeCoupon extends AbstractOrderItemType implements OrderItemTypeCouponInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'coupon';
    }
}