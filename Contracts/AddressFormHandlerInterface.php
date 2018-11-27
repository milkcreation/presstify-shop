<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Form\FactoryRequest;
use tiFy\Contracts\Form\FormFactory;

interface AddressFormHandlerInterface
{
    /**
     * Instanciation de l'addon.
     *
     * @param $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param FormFactory $form Instance du formulaire associé.
     *
     * @return $this
     */
    public function __invoke($name, $attrs = [], FormFactory $form);

    /**
     * Court-circuitage de la requête au moment de sa soumission.
     *
     * @return void
     */
    public function onRequestSubmit(FactoryRequest $request);
}