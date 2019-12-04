<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Addresses;

use tiFy\Contracts\Form\FactoryRequest;
use tiFy\Form\AddonFactory;
use tiFy\Plugins\Shop\Contracts\{Address, AddressFormHandler as AddressFormHandlerContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;

class FormHandler extends AddonFactory implements AddressFormHandlerContract
{
    use ShopAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->form()->events()->listen('request.submit', [$this, 'onRequestSubmit']);
    }

    /**
     * @inheritdoc
     */
    public function onRequestSubmit(FactoryRequest $request)
    {
        // Récupération du contrôleur valide
        $ctrl = $this->params('controller', '');
        if (!$ctrl instanceof Address) {
            return;
        }

        // Récupération des données utilisateur à sauvegarder.
        $userdata = [];
        $session_data = [];

        foreach ($request->keys() as $key) {
            $slug = preg_replace('#^' . $ctrl->getId() . '_#', '', $key);

            if (($field = $request->field($slug)) && $field->supports('transport')) {
                $value = $request->field($slug)->getValue();
                $session_data[$slug] = $value;
                $userdata[$key] = $value;
            }
        }

        // Sauvegarde des données en session.
        $this->shop()->session()->put($ctrl->getId(), $session_data);

        // Sauvegarde des données de compte utilisateur.
        $user = $this->shop()->users()->get();

        if ($user->isLoggedIn()) {
            foreach ($userdata as $k => $v) {
                update_user_option($user->getId(), $k, $v, !is_multisite());
            }
        }
    }
}