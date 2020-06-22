<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Form;

use tiFy\Contracts\{
    Http\Response as ResponseContract,
    Form\FactoryField
};
use tiFy\Form\FormFactory;
use tiFy\Plugins\Shop\{
    Contracts\AddressesForm as AddressesFormContract,
    ShopAwareTrait
};
use tiFy\Http\Response;
use tiFy\Support\Proxy\{Request, Redirect};
use tiFy\Validation\Validator as v;

class AddressesForm extends FormFactory implements AddressesFormContract
{
    use ShopAwareTrait;

    /**
     * Indicateur de traitement automatique.
     * @var boolean|null
     */
    protected $auto = false;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $user = $this->shop->user();
        $session = $this->shop->session();

        $fields = [
            'billing_title'      => [
                'value'   => '<h3>' . __('Adresse de facturation', 'tify') . '</h3>',
                'wrapper' => true,
            ],
            'billing_last_name'  => [
                'required' => true,
                'title'    => __('Nom de famille', 'tify'),
                'type'     => 'text',
                'value'    => $session->get('billing.last_name', $user->getBilling('last_name', $user->getLastName())),
            ],
            'billing_first_name' => [
                'required' => true,
                'title'    => __('Prénom', 'tify'),
                'type'     => 'text',
                'value'    => $session->get('billing.first_name',
                    $user->getBilling('first_name', $user->getFirstName())
                ),
            ],
            'billing_company'    => [
                'title' => __('Société', 'tify'),
                'type'  => 'text',
                'value' => $session->get('billing.company', $user->getBilling('company')),
            ],
            'billing_address1'   => [
                'required' => true,
                'title'    => __('Adresse postale', 'tify'),
                'type'     => 'text',
                'value'    => $session->get('billing.address1', $user->getBilling('address1')),
            ],
            'billing_address2'   => [
                'required' => true,
                'title'    => __('Complément d\'adresse', 'tify'),
                'type'     => 'text',
                'value'    => $session->get('billing.address2', $user->getBilling('address2')),
            ],
            'billing_postcode'   => [
                'required' => true,
                'title'    => __('Code postal', 'tify'),
                'type'     => 'text',
                'value'    => $session->get('billing.postcode', $user->getBilling('postcode')),
            ],
            'billing_city'       => [
                'required' => true,
                'title'    => __('Ville', 'tify'),
                'type'     => 'text',
                'value'    => $session->get('billing.city', $user->getBilling('city')),
            ],
            'billing_phone'      => [
                'required' => true,
                'title'    => __('Numéro de téléphone', 'tify'),
                'type'     => 'text',
                'value'    => $session->get('billing.phone', $user->getBilling('phone')),
            ],
            'billing_email'      => [
                'required' => true,
                'title'    => __('Adresse de messagerie', 'tify'),
                'type'     => 'text',
                'value'    => $session->get('billing.email', $user->getBilling('email', $user->getEmail())),
            ],
        ];

        if ($this->shop->settings()->isShippingEnabled()) {
            $fields = array_merge($fields, [
                'shipping_as_billing' => [
                    'label' => [
                        'content'  => __('Identique à l\'adresse de facturation', 'tify'),
                        'position' => 'after',
                    ],
                    'type'  => 'checkbox',
                    'value' => $session->get('shipping.as_billing', $user->getShipping('as_billing', 'on')),
                ],
                'shipping_title'      => [
                    'value'   => '<h3>' . __('Adresse de livraison', 'tify') . '</h3>',
                    'wrapper' => true,
                ],
                'shipping_last_name'  => [
                    'required' => true,
                    'title'    => __('Nom de famille', 'tify'),
                    'type'     => 'text',
                    'value'    => $session->get('shipping.last_name',
                        $user->getShipping('last_name', $user->getLastName())
                    ),
                ],
                'shipping_first_name' => [
                    'required' => true,
                    'title'    => __('Prénom', 'tify'),
                    'type'     => 'text',
                    'value'    => $session->get('shipping.first_name',
                        $user->getShipping('first_name', $user->getFirstName())
                    ),
                ],
                'shipping_company'    => [
                    'required' => true,
                    'title'    => __('Société', 'tify'),
                    'type'     => 'text',
                    'value'    => $session->get('shipping.company', $user->getShipping('company')),
                ],
                'shipping_address1'   => [
                    'required' => true,
                    'title'    => __('Adresse postale', 'tify'),
                    'type'     => 'text',
                    'value'    => $session->get('shipping.address1', $user->getShipping('address1')),
                ],
                'shipping_address2'   => [
                    'required' => true,
                    'title'    => __('Complément d\'adresse', 'tify'),
                    'type'     => 'text',
                    'value'    => $session->get('shipping.address2', $user->getShipping('address2')),
                ],
                'shipping_postcode'   => [
                    'required' => true,
                    'title'    => __('Code postal', 'tify'),
                    'type'     => 'text',
                    'value'    => $session->get('shipping.postcode', $user->getShipping('postcode')),
                ],
                'shipping_city'       => [
                    'required' => true,
                    'title'    => __('Ville', 'tify'),
                    'type'     => 'text',
                    'value'    => $session->get('shipping.city', $user->getShipping('city')),
                ],
            ]);
        }

        $this->set(compact('fields'), [
            'buttons' => [
                'submit' => [
                    'extras' => [
                        'content' => __('Enregistrer les modifications', 'tify'),
                        'type'    => 'submit',
                    ],
                    'group'  => 'submit',
                    'type'   => 'button',
                ],
            ],
            'notices' => [
                'success' => [
                    'message' => __('Vos adresses ont été mises à jour avec succès.', 'tify'),
                ],
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function proceed(): void
    {
        /* Sauvegarde des données de session. */
        $session = ['billing' => [], 'shipping' => []];

        foreach ($this->fields() as $slug => $field) {
            if (preg_match('/(billing|shipping)_(.*)/', $slug, $m)) {
                $session[$m[1]][$m[2]] = $field->getValue();
            }
        }

        foreach($session as $key => $value) {
            $this->shop->session()->put($key, $value);
        }
        /**/

        /** Sauvegarde des données du compte utilisateur. * /
        $user = $this->shop()->users()->get();

        if ($user->isLoggedIn()) {
            foreach ($userdata as $k => $v) {
                update_user_option($user->getId(), $k, $v, !is_multisite());
            }
        }
        /**/

        $this->shop->notices()->add(__('Vos informations personnelles ont été enregistrées avec succès.', 'theme'));
    }

    /**
     * @inheritDoc
     */
    public function response(): ResponseContract
    {
        $this->prepare();

        if (Request::isMethod('post')) {
            if ($this->validate()) {
                $this->proceed();

                return Redirect::to($this->shop()->functions()->page()->checkoutPageUrl());
            }
        }

        return new Response($this->view());
    }

    /**
     * @inheritDoc
     */
    public function validate(): bool
    {
        $this->request()->prepare();

        /** @var FactoryField[] $fields */
        $fields = $this->fields()->all();

        if (!$this->request()->verify()) {
            $this->error(__('Une erreur est survenue, impossible de valider votre demande de contact.', 'tify'));
        } else {
            if (isset($fields['billing_last_name']) && !v::notEmpty()->validate($this->request()->get('billing_last_name'))) {
                $fields['billing_last_name']->addError(__('Veuillez renseigner votre nom de famille.', 'tify'));
            }

            if (isset($fields['billing_first_name']) && !v::notEmpty()->validate($this->request()->get('billing_first_name'))) {
                $fields['billing_first_name']->addError(__('Veuillez renseigner votre prénom.', 'tify'));
            }

            if (isset($fields['billing_address1']) && !v::notEmpty()->validate($this->request()->get('billing_address1'))) {
                $fields['billing_address1']->addError(__('Veuillez renseigner votre adresse postale.', 'tify'));
            }

            if (isset($fields['billing_postcode']) && !v::notEmpty()->validate($this->request()->get('billing_postcode'))) {
                $fields['billing_postcode']->addError(__('Veuillez renseigner votre code postal.', 'tify'));
            }

            if (isset($fields['billing_city']) && !v::notEmpty()->validate($this->request()->get('billing_city'))) {
                $fields['billing_city']->addError(__('Veuillez renseigner votre ville.', 'tify'));
            }

            if (isset($fields['billing_phone']) && !v::notEmpty()->validate($this->request()->get('billing_phone'))) {
                $fields['billing_phone']->addError(__('Veuillez renseigner votre numéro de téléphone.', 'tify'));
            }

            if (isset($fields['billing_email'])) {
                $email = $this->request()->get('billing_email');
                if (!v::notEmpty()->validate($email)) {
                    $fields['billing_email']->addError(__('Veuillez renseigner votre adresse de messagerie.', 'tify'));
                } elseif (!v::email()->validate($email)) {
                    $fields['billing_email']->addError(
                        __('L\'adresse de messagerie renseignée n\'est pas un e-mail valide.', 'tify')
                    );
                }
            }

            foreach ($fields as $slug => $field) {
                if ($field->supports('transport')) {
                    $field->setValue($this->request()->get($slug));
                }
            }
        }

        return !$this->hasError();
    }

    /**
     * @inheritDoc
     */
    public function view(): string
    {
        return $this->render();
    }
}