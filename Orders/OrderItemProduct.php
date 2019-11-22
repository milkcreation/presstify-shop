<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\{OrderItemProduct as OrderItemProductContract};
use tiFy\Support\ParamsBag;

class OrderItemProduct extends AbstractOrderItem implements OrderItemProductContract
{
    /**
     * Cartographie des attributs en correspondance avec les métadonnées enregistrées en base.
     * @var array
     */
    protected $metasMap = [
        'quantity'           => '_qty',
        'product_id'         => '_product_id',
        'product_sku'        => '_product_sku',
        'product'            => '_product',
        'purchasing_options' => '_purchasing_options',
        'subtotal'           => '_line_subtotal',
        'subtotal_class'     => '_line_subtotal_tax',
        'tax_class'          => '_tax_class',
        'taxes'              => '_line_tax_data',
        'total'              => '_line_total',
        'total_tax'          => '_line_tax',
        'variation_id'       => '_variation_id'
    ];

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'id'           => 0,
            'name'         => '',
            'order_id'     => 0,
            'product_id'   => 0,
            'quantity'     => 1,
            'subtotal'     => 0,
            'subtotal_tax' => 0,
            'tax_class'    => '',
            'taxes'        => [
                'subtotal' => [],
                'total'    => [],
            ],
            'total'        => 0,
            'total_tax'    => 0,
            'type'         => '',
            'variation_id' => 0,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getProduct(): ParamsBag
    {
        return (new ParamsBag())->set($this->get('product', []));
    }

    /**
     * @inheritDoc
     */
    public function getProductId(): int
    {
        return (int)$this->get('product_id', 0);
    }

    /**
     * @inheritDoc
     */
    public function getPurchasingOptions(): array
    {
        return $this->get('purchasing_options', []);
    }

    /**
     * @inheritDoc
     */
    public function getQuantity(): int
    {
        return (int)$this->get('quantity', 0);
    }

    /**
     * @inheritDoc
     */
    public function getSubtotal(): float
    {
        return (float)$this->get('subtotal', 0);
    }

    /**
     * @inheritDoc
     */
    public function getSubtotalTax(): float
    {
        return (float)$this->get('subtotal_tax', 0);
    }

    /**
     * @inheritDoc
     */
    public function getTaxClass(): string
    {
        return (string)$this->get('tax_class', '');
    }

    /**
     * @inheritDoc
     */
    public function getTaxes(): array
    {
        return (array)$this->get('taxes', []);
    }

    /**
     * @inheritDoc
     */
    public function getTotal(): float
    {
        return (float)$this->get('total', 0);
    }

    /**
     * @inheritDoc
     */
    public function getTotalTax(): float
    {
        return (float)$this->get('total_tax', 0);
    }

    /**
     * @inheritDoc
     */
    public function getVariationId(): int
    {
        return (int)$this->get('variation_id', 0);
    }
}