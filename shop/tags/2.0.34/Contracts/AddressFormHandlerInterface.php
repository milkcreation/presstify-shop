<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Form\FactoryRequest;

interface AddressFormHandlerInterface
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