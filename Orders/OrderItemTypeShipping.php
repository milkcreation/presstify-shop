<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeShipping as OrderItemTypeShippingContract;

class OrderItemTypeShipping extends OrderItemType implements OrderItemTypeShippingContract
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'shipping';
    }
}