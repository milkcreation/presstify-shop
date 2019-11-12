<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeFee as OrderItemTypeFeeContract;

class OrderItemTypeFee extends  OrderItemType implements OrderItemTypeFeeContract
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'fee';
    }
}