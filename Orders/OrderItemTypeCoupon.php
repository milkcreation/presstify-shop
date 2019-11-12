<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeCoupon as OrderItemTypeCouponContract;

class OrderItemTypeCoupon extends OrderItemType implements OrderItemTypeCouponContract
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'coupon';
    }
}