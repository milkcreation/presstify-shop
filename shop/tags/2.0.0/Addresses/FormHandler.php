<?php

namespace tiFy\Plugins\Shop\Addresses;

use tiFy\Form\Addons\AbstractAddonController;
use tiFy\Form\Forms\FormHandleController;
use tiFy\Plugins\Shop\Shop;

class FormHandler extends AbstractAddonController implements FormHandlerInterface
{
    /**
     * Identifiant de qualification de l'addon
     * @var string
     */
    public $id = 'tify_shop_address_form_handler';

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Définition des fonctions de callback
        $this->callbacks['handle_submit_request'] = [$this, 'cb_handle_submit_request'];
    }

    /**
     * Traitement de la requête de formulaire.
     *
     * @param FormHandleController $handle Controleur de traitement des formulaires.
     *
     * @return void
     */
    public function cb_handle_submit_request($handle)
    {
        /** @var AddressInterface $ctrl */
        if (!$ctrl = $this->getFormAttr('controller', '')) :
            return;
        endif;

        $user_data = []; $session_data = [];
        foreach ($handle->getFieldsVars() as $slug => $value) :
            $key = preg_replace('#^' . $ctrl->getId() . '_#', '', $slug);
            $session_data[$key] = $value;
            $user_data[$slug] = $value;
        endforeach;

        // Sauvegarde des données en session
        $this->shop->session()->put($ctrl->getId(), $session_data);
        $this->shop->session()->save();

        // Sauvegarde des données de compte utilisateur
        $current_user = $this->shop->users()->get();
        if ($current_user->isLoggedIn()) :
            foreach($user_data as $key => $v) :
                \update_user_meta($current_user->getId(), $key, $v);
            endforeach;
        endif;
    }
}