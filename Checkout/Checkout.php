<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Checkout;

use tiFy\Plugins\Shop\Contracts\{Checkout as CheckoutContract, Order};
use tiFy\Plugins\Shop\ShopAwareTrait;

class Checkout implements CheckoutContract
{
    use ShopAwareTrait;

    /**
     * @inheritDoc
     */
    public function createOrderItemsCoupon(Order $order): void { }

    /**
     * @inheritDoc
     */
    public function createOrderItemsDiscount(Order $order): void { }

    /**
     * @inheritDoc
     */
    public function createOrderItemsFee(Order $order): void { }

    /**
     * @inheritDoc
     */
    public function createOrderItemsProduct(Order $order): void
    {
        if ($lines = $this->shop->cart()->all()) {
            foreach ($lines as $line) {
                $product = $line->getProduct();

                $args = [
                    'name'         => $product->getTitle(),
                    'product'      => $product->all(),
                    'product_id'   => $product->getId(),
                    'product_sku'  => $product->getSku(),
                    'quantity'     => $line->getQuantity(),
                    'subtotal'     => $line->getSubtotal(),
                    'subtotal_tax' => $line->getSubtotalTax(),
                    'total'        => $line->getTotal(),
                    'total_tax'    => $line->getTax(),
                    'tax_class'    => '',
                    'taxes'        => [],
                    'variation'    => '',
                    'variation_id' => 0,
                ];

                $purchasing_options = [];
                foreach ($line->get('purchasing_options', []) as $product_id => $opts) {
                    if ($prod = $this->shop->product($product_id)) {
                        $purchasing_options[$product_id] = [];
                        foreach ($opts as $name => $opt) {
                            if ($po = $prod->getPurchasingOption($name)) {
                                $po->setSelected($opt);
                                $purchasing_options[$product_id][$po->getName()] = [
                                    'selected' => $opt,
                                    'render'   => trim((string)$po->renderCartLine()),
                                    'sku'      => $prod->getSku(),
                                ];
                            }
                        }
                    }
                }
                $args['purchasing_options'] = $purchasing_options;

                $order->addOrderItem($order->createItemProduct($args));
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function createOrderItemsShipping(Order $order): void { }

    /**
     * @inheritDoc
     */
    public function createOrderItemsTax(Order $order): void { }

    /**
     * @inheritDoc
     */
    public function handleUrl(): string
    {
        return $this->shop->route()->checkoutHandleUrl();
    }
}