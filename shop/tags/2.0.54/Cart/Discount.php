<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Cart;

use InvalidArgumentException;
use tiFy\Plugins\Shop\Contracts\{Cart as CartContract, CartDiscount as DiscountContract};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\ParamsBag;

class Discount implements DiscountContract
{
    use ShopAwareTrait;

    /**
     * Instance des paramètres associés.
     * @var ParamsBag
     */
    protected $params;

    /**
     * Instance du panier de commande associé.
     * @var CartContract|null|false
     */
    protected $cart;

    /**
     * @inheritDoc
     */
    public function calculate(): float
    {
        if (($cart = $this->cart()) && $this->isValid()) {
            return floor($cart->collect()->sum('line_total') / $this->getTrigger()) * $this->getAmount();
        }

        return 0;
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
    public function defaultsParams(): array
    {
        return [
            'trigger' => 0,
            'amount'  => 0,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAmount(): float
    {
        return (float)$this->params('amount', 0);
    }

    /**
     * @inheritDoc
     */
    public function getTrigger(): float
    {
        return (float)$this->params('trigger', 0);
    }

    /**
     * @inheritDoc
     */
    public function params($key = null, $default = null)
    {
        if (!$this->params instanceof ParamsBag) {
            $this->params = new ParamsBag();

            $this->parseParams();
        }

        if (is_null($key)) {
            return $this->params;
        } elseif (is_string($key)) {
            return $this->params->get($key, $default);
        } elseif (is_array($key)) {
            return $this->params->set($key);
        } else {
            throw new InvalidArgumentException(
                __('L\'appel aux paramètres de l\'instance de remise du panier d\'achat est invalide.', 'tify')
            );
        }
    }

    /**
     * Vérification d'existance d'une remise valide.
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        return !!$this->getTrigger() && !!$this->getAmount();
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): void
    {
        $this->params()->set($this->defaultsParams());
    }

    /**
     * @inheritDoc
     */
    public function setAmount(float $amount): DiscountContract
    {
        $this->params(compact('amount'));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTrigger(float $trigger): DiscountContract
    {
        $this->params(compact('trigger'));

        return $this;
    }
}