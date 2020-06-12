<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Form\FormFactory;

interface Form extends ShopAwareTrait
{
    /**
     * Instance du formulaire des adresses de facturation et livraison.
     *
     * @return FormFactory
     */
    public function addresses(): ?FormFactory;
}