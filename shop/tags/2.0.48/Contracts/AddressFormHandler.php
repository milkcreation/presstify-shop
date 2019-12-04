<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Form\{AddonFactory, FactoryRequest};

interface AddressFormHandler extends AddonFactory, ShopAwareTrait
{
    /**
     * Court-circuitage de la requête au moment de sa soumission.
     *
     * @param FactoryRequest $request
     *
     * @return void
     */
    public function onRequestSubmit(FactoryRequest $request);
}