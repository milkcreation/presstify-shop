<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Form\Controller\Handle;

interface AddressFormHandlerInterface
{
    /**
     * Traitement de la requête de formulaire
     *
     * @param Handle $handle Controleur de traitement des formulaires
     *
     * @return void
     */
    public function cb_handle_submit_request($handle);
}