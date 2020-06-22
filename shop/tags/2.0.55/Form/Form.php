<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Form;

use tiFy\Contracts\Form\FormFactory;
use tiFy\Plugins\Shop\{Contracts\Form as FormContract, ShopAwareTrait};
use tiFy\Support\Proxy\Form as f;

class Form implements FormContract
{
    use ShopAwareTrait;

    /**
     * Instance du formulaire des adresses (Livraison + Facturation).
     * @return FormFactory|null|false
     */
    protected $addresses;

    /**
     * @inheritDoc
     */
    public function addresses(): ?FormFactory
    {
        if (is_null($this->addresses)) {
            f::set('shop.addresses', $this->shop->resolve('form.addresses'));

            $this->addresses = f::get('shop.addresses') ?: false;
        }

        return $this->addresses ?: null;
    }
}