<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeProduct as OrderItemTypeProductContract;
use tiFy\Support\ParamsBag;

class OrderItemTypeProduct extends OrderItemType implements OrderItemTypeProductContract
{
    /**
     * Cartographie des attributs en correspondance avec les métadonnées enregistrées en base.
     * @var array
     */
    protected $metasMap = [
        'product_id'         => '_product_id',
        'product_sku'        => '_product_sku',
        'product'            => '_product',
        'variation_id'       => '_variation_id',
        'quantity'           => '_qty',
        'tax_class'          => '_tax_class',
        'subtotal'           => '_line_subtotal',
        'subtotal_class'     => '_line_subtotal_tax',
        'total'              => '_line_total',
        'total_tax'          => '_line_tax',
        'taxes'              => '_line_tax_data',
        'purchasing_options' => '_purchasing_options'
    ];

    /**
     * Liste des attributs.
     * @var array
     */
    protected $attributes = [
        'id'           => 0,
        'name'         => '',
        'type'         => '',
        'order_id'     => 0,
        'product_id'   => 0,
        'variation_id' => 0,
        'quantity'     => 1,
        'tax_class'    => '',
        'subtotal'     => 0,
        'subtotal_tax' => 0,
        'total'        => 0,
        'total_tax'    => 0,
        'taxes'        => [
            'subtotal' => [],
            'total'    => [],
        ]
    ];

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
    public function getType(): string
    {
        return 'line_item';
    }

    /**
     * @inheritDoc
     */
    public function getVariationId(): int
    {
        return (int)$this->get('variation_id', 0);
    }
}