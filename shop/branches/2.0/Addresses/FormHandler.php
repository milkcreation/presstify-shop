<?php

/**
 * @name FormHandler
 * @desc Traitement des formulaires d'adresses.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Addresses;

use tiFy\Form\Addons\AbstractAddonController;
use tiFy\Form\Forms\FormHandleController;
use tiFy\Plugins\Shop\Contracts\AddressFormHandlerInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class FormHandler extends AbstractAddonController implements AddressFormHandlerInterface
{
    use ShopResolverTrait;

    /**
     * Identifiant de qualification de l'addon.
     * @var string
     */
    public $name = 'tify_shop_address_form_handler';

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;

        $this->callbacks['handle_submit_request'] = [$this, 'cb_handle_submit_request'];
    }

    /**
     * {@inheritdoc}
     */
    public function cb_handle_submit_request($handle)
    {
        /** @var AddressInterface $ctrl */
        if (!$ctrl = $this->getFormOption('controller', '')) :
            return;
        endif;

        $user_data = []; $session_data = [];
        foreach ($handle->allFieldVars() as $slug => $value) :
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