<?php

/**
 * @name OrderItemProduct
 * @desc Controleur d'une ligne d'article associée à une commande.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Orders\OrderItems\AbstractOrderItemType;
use tiFy\Plugins\Shop\Contracts\OrderItemTypeProductInterface;
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;
use tiFy\Plugins\Shop\Contracts\ProductsInterface;

class OrderItemTypeProduct extends AbstractOrderItemType implements OrderItemTypeProductInterface
{
    /**
     * Cartographie des attributs en correspondance avec les métadonnées enregistrées en base.
     * @var array
     */
    protected $metas_map = [
        'product_id'     => '_product_id',
        'product'        => '_product',
        'variation_id'   => '_variation_id',
        'quantity'       => '_qty',
        'tax_class'      => '_tax_class',
        'subtotal'       => '_line_subtotal',
        'subtotal_class' => '_line_subtotal_tax',
        'total'          => '_line_total',
        'total_tax'      => '_line_tax',
        'taxes'          => '_line_tax_data'
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
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return app('params.bag', [$this->get('product', [])]);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return (int)$this->get('product_id', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return (int)$this->get('quantity', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotal()
    {
        return (float)$this->get('subtotal', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalTax()
    {
        return (float)$this->get('subtotal_tax', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxClass()
    {
        return (string)$this->get('tax_class', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxes()
    {
        return (array)$this->get('taxes', []);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return (float)$this->get('total', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalTax()
    {
        return (float)$this->get('total_tax', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'line_item';
    }

    /**
     * {@inheritdoc}
     */
    public function getVariationId()
    {
        return (int)$this->get('variation_id', 0);
    }
}