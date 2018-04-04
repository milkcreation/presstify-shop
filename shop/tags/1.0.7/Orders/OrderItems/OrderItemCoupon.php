<?php

/**
 * @name OrderItemCoupon
 * @desc Controleur d'une ligne d'un coupon de réduction associé à une commande
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Orders\OrderItems
 * @version 1.1
 * @since 1.1
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

final class OrderItemCoupon extends AbstractOrderItem implements OrderItemCouponInterface
{
    /**
     * Récupération du type d'élement associé à la commande.
     *
     * @return string coupon
     */
    public function getType()
    {
        return 'coupon';
    }
}