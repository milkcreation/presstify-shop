<?php

/**
 * @name FormHandler
 * @desc Traitement des formulaires d'adresses.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Addresses;

use tiFy\Contracts\Form\FactoryRequest;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\AddonController;
use tiFy\Plugins\Shop\Contracts\AddressInterface;
use tiFy\Plugins\Shop\Contracts\AddressFormHandlerInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class FormHandler extends AddonController implements AddressFormHandlerInterface
{
    use ShopResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($name, $attrs = [], FormFactory $form)
    {
        parent::__construct($name, $attrs, $form);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->events()->listen('request.submit', [$this, 'onRequestSubmit']);
    }

    /**
     * {@inheritdoc}
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
        $this->shop()->session()->put($ctrl->getId(), $session_data);
        $this->shop()->session()->save();

        // Sauvegarde des données de compte utilisateur.
        $user = $this->shop()->users()->getItem();
        if ($user->isLoggedIn()) :
            foreach($userdata as $key => $v) :
                update_user_option($user->getId(), $key, $v, is_multisite());
            endforeach;
        endif;
    }
}