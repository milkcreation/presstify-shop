<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Cart;

use tiFy\Plugins\Shop\Contracts\{Cart, CartLine as CartLineContract, Product};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Router;

class Line extends ParamsBag implements CartLineContract
{
    use ShopAwareTrait;

    /**
     * Instance du panier de commande associé.
     * @var Cart
     */
    protected $cart;

    /**
     * CONSTRUCTEUR.
     *
     * @param Cart $cart
     *
     * @return void
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;

        $this->setShop($this->cart->shop());
    }

    /**
     * @inheritDoc
     */
    public function cart(): Cart
    {
        return $this->cart;
    }

    /**
     * @inheritDoc
     */
    public function cartFieldName(string $attribute_name): string
    {
        return "cart[{$this->getKey()}][{$attribute_name}]";
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return (string)$this->get('key', '');
    }

    /**
     * @inheritDoc
     */
    public function getPrice(): float
    {
        return (float)$this->getProduct()->getRegularPrice() * $this->getQuantity();
    }

    /**
     * @inheritDoc
     */
    public function getPriceHtml(): string
    {
        return $this->shop()->functions()->price()->html($this->getPrice());
    }

    /**
     * @inheritDoc
     */
    public function getPriceIncludesTax(): ?string
    {
        return $this->get('price_includes_tax', null);
    }

    /**
     * @inheritDoc
     */
    public function getProduct(): ?Product
    {
        return $this->get('product', null);
    }

    /**
     * @inheritDoc
     */
    public function getProductId(): int
    {
        return (int)$this->getProduct()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getPurchasingOptions(): array
    {
        $purchasing_options = [];

        foreach ($this->get('purchasing_options', []) as $product_id => $opts) {
            if ($product = $this->shop()->product($product_id)) {
                foreach ($opts as $name => $selected) {
                    if ($po = $product->getPurchasingOption($name)) {
                        $po->setSelected($selected);
                        $purchasing_options[$product_id][] = $po;
                    }
                }
            }
        }

        return $purchasing_options;
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
        return (float)$this->get('line_subtotal', 0);
    }

    /**
     * @inheritDoc
     */
    public function getSubtotalTax(): float
    {
        return (float)$this->get('line_subtotal_tax', 0);
    }

    /**
     * @inheritDoc
     */
    public function getTax(): float
    {
        return (float)$this->get('line_tax', 0);
    }

    /**
     * @inheritDoc
     */
    public function getTaxable(): ?string
    {
        return $this->get('taxable', null);
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
        return (array)$this->get('line_tax_data', ['subtotal' => 0, 'total' => 0]);
    }

    /**
     * @inheritDoc
     */
    public function getTaxRates(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getTotal(): float
    {
        return (float)$this->get('line_total', 0);
    }


    /**
     * @inheritDoc
     */
    public function parse(): CartLineContract
    {
        parent::parse();

        if ($this->getProduct()) {
            $this['product_id'] = $this->getProductId();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function needShipping(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function removeUrl(): string
    {
        return Router::url('shop.cart.remove', [$this->getKey()]);
    }
}