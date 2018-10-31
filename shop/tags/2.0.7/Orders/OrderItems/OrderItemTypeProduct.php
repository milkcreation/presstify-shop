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
     * Classe de rappel du produit associé.
     * @var ProductItemInterface
     */
    protected $product;

    /**
     * Cartographie des attributs en correspondance avec les métadonnées enregistrées en base.
     * @var array
     */
    protected $metas_map = [
        'product_id'     => '_product_id',
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
     * Récupération de la classe de rappel du produit associé.
     *
     * @return null|ProductItemInterface
     */
    public function getProduct()
    {
        if ($this->product instanceof ProductItemInterface) :
            return $this->product;
        elseif ($id = $this->getProductId()) :
            /** @var ProductsInterface $products */
            $products = app('shop.products.controller');

            return $this->product = $products->getItem($id);
        else :
            return null;
        endif;
    }

    /**
     * Récupération de l'identifiant de qualification du produit associé.
     *
     * @return int
     */
    public function getProductId()
    {
        return (int)$this->get('product_id', 0);
    }

    /**
     * Récupération de la quantité d'article du produit associé.
     *
     * @return int
     */
    public function getQuantity()
    {
        return (int)$this->get('quantity', 0);
    }

    /**
     * Récupération du montant du sous-total de la commande.
     *
     * @return float
     */
    public function getSubtotal()
    {
        return (float)$this->get('subtotal', 0);
    }

    /**
     * Récupération du montant du sous-total appliqué à la commande.
     *
     * @return float
     */
    public function getSubtotalTax()
    {
        return (float)$this->get('subtotal_tax', 0);
    }

    /**
     * Récupération de la classe de taxe appliqué à la commande.
     *
     * @return string
     */
    public function getTaxClass()
    {
        return (string)$this->get('tax_class', '');
    }

    /**
     * Récupération de la liste des taxes appliquées à la commande.
     *
     * @return array
     */
    public function getTaxes()
    {
        return (array)$this->get('taxes', []);
    }

    /**
     * Récupération du montant total de la commande.
     *
     * @return float
     */
    public function getTotal()
    {
        return (float)$this->get('total', 0);
    }

    /**
     * Récupération du montant total de la taxe appliqué à la commande.
     *
     * @return float
     */
    public function getTotalTax()
    {
        return (float)$this->get('total_tax', 0);
    }

    /**
     * Récupération du type d'élement associé à la commande.
     *
     * @return string line_item
     */
    public function getType()
    {
        return 'line_item';
    }

    /**
     * Récupération de l'identifiant de qualification de la variation de produit associée.
     *
     * @return int
     */
    public function getVariationId()
    {
        return (int)$this->get('variation_id', 0);
    }
}