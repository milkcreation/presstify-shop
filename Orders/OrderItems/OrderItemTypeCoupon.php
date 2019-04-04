<?php

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeCouponInterface;

/**
 * Class OrderItemTypeCoupon
 *
 * @desc Controleur d'une ligne d'un coupon de réduction associé à une commande.
 */
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