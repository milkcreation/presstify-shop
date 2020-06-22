<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Cart;

use tiFy\Plugins\Shop\Contracts\{Cart as CartContract, CartTotal as CartTotalContract};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\ParamsBag;

class Total extends ParamsBag implements CartTotalContract
{
    use ShopAwareTrait;

    /**
     * Instance du panier de commande associé.
     * @var CartContract|null|false
     */
    protected $cart;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return (string)$this->getGlobal();
    }

    /**
     * @inheritDoc
     */
    public function cart(): ?CartContract
    {
        if (is_null($this->cart)) {
            $this->cart = $this->shop()->cart() ?? false;
        }

        return $this->cart ?? null;
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'lines_subtotal'     => 0,
            'lines_subtotal_tax' => 0,
            'lines_total'        => 0,
            'lines_total_tax'    => 0,
            'lines_taxes'        => [],
            'total'              => 0,
            'shipping_total'     => 0,
            'shipping_tax_total' => 0,
            'shipping_taxes'     => [],
            'discount_total'     => 0,
            'fee_total'          => 0,
            'fee_total_tax'      => 0,
            'fee_taxes'          => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDiscountTax(): float
    {
        return (float)$this->get('discount_tax', 0);
    }

    /**
     * @inheritDoc
     */
    public function getDiscountTotal(): float
    {
        return (float)$this->get('discount_total', 0);
    }

    /**
     * @inheritDoc
     */
    public function getFeeTax(): float
    {
        return (float)$this->get('fee_total_tax', 0);
    }

    /**
     * @inheritDoc
     */
    public function getFeeTaxes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getFeeTotal(): float
    {
        return (float)$this->get('fee_total', 0);
    }

    /**
     * @inheritDoc
     */
    public function getGlobal(): float
    {
        return (float)$this->get('total', 0);
    }

    /**
     * @inheritDoc
     */
    public function getGlobalTax(): float
    {
        return (float)$this->get('total_tax', 0);
    }

    /**
     * @inheritDoc
     */
    public function getLinesSubtotal(): float
    {
        return (float)$this->get('lines_subtotal', 0);
    }

    /**
     * @inheritDoc
     */
    public function getLinesSubtotalTax(): float
    {
        return (float)$this->get('lines_subtotal_tax', 0);
    }

    /**
     * @inheritDoc
     */
    public function getLinesTaxes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getLinesTotal(): float
    {
        return (float)$this->get('lines_total', 0);
    }

    /**
     * @inheritDoc
     */
    public function getLinesTotalTax(): float
    {
        return (float)$this->get('lines_total_tax', 0);
    }

    /**
     * @inheritDoc
     */
    public function getShippingTax(): float
    {
        return (float)$this->get('shipping_tax_total', 0);
    }

    /**
     * @inheritDoc
     */
    public function getShippingTaxes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getShippingTotal(): float
    {
        return (float)$this->get('shipping_total', 0);
    }

    /**
     * @inheritDoc
     */
    public function parse(): CartTotalContract
    {
        parent::parse();

        if ($lines = $this->cart()->all()) {
            foreach ($lines as $line) {
                // Sous-totaux
                $line['line_tax_data'] = ['subtotal' => []];
                $line['line_subtotal'] = $line->getPrice();
                $line['line_subtotal_tax'] = 0;

                // Totaux
                $line['line_tax_data'] = array_merge($line['line_tax_data'], ['total' => []]);
                $line['line_total'] = $line->getPrice();
                $line['line_tax'] = 0;
            }

            // Calcul des sous-totaux
            $this['lines_subtotal'] = $this->cart()->collect()->sum('line_subtotal');
            $this['lines_subtotal_tax'] = $this->cart()->collect()->sum('line_subtotal_tax');

            // Calcul des totaux
            $this['lines_total'] = $this->cart()->collect()->sum('line_total');
            $this['lines_total_tax'] = $this->cart()->collect()->sum('line_tax');

            $this['total'] = $this->getLinesTotal() + $this->getFeeTotal() + $this->getShippingTotal() - $this->getDiscountTotal();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCart(CartContract $cart): CartTotalContract
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDiscountTotal(float $discount): CartTotalContract
    {
        $this->set('discount_total', $discount);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFeeTotal(float $fee): CartTotalContract
    {
        $this->set('fee_total', $fee);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setShippingTotal(float $shipping): CartTotalContract
    {
        $this->set('shipping_total', $shipping);

        return $this;
    }
}