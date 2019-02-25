<?php

namespace tiFy\Plugins\Shop\Addresses;

use tiFy\Contracts\Form\FactoryRequest;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\AddonController;
use tiFy\Plugins\Shop\Contracts\AddressInterface;
use tiFy\Plugins\Shop\Contracts\AddressFormHandlerInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

/**
 * Class FormHandler
 * @desc Traitement des formulaires d'adresses.
 */
class FormHandler extends AddonController implements AddressFormHandlerInterface
{
    use ShopResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param FormFactory $form Formulaire associé.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], FormFactory $form, Shop $shop)
    {
        $this->shop = $shop;

        parent::__construct($name, $attrs, $form);
    }

    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->events()->listen('request.submit', [$this, 'onRequestSubmit']);
    }

    /**
     * @inheritdoc
     */
    public function onRequestSubmit(FactoryRequest $request)
    {
        // Récupération du contrôleur valide
        $ctrl = $this->get('controller', '');
        if (!$ctrl instanceof AddressInterface) :
            return;
        endif;

        // Récupération des données utilisateur à sauvegarder.
        $userdata = []; $session_data = [];

        foreach ($request->all() as $key => $value) :
            $slug = preg_replace('#^' . $ctrl->getId() . '_#', '', $key);

            if (($field = $request->field($slug)) && $field->supports('transport')) :
                $session_data[$slug] = $value;
                $userdata[$key] = $value;
            endif;
        endforeach;

        // Sauvegarde des données en session.
        $this->session()->put($ctrl->getId(), $session_data);
        $this->session()->save();

        // Sauvegarde des données de compte utilisateur.
        $user = $this->users()->getItem();

        if ($user->isLoggedIn()) :
            foreach($userdata as $k => $v) :
                update_user_option($user->getId(), $k, $v, !is_multisite());
            endforeach;
        endif;
    }
}