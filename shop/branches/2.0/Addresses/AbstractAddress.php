<?php

namespace tiFy\Plugins\Shop\Addresses;

use Illuminate\Support\Str;
use tiFy\Form\Form;
use tiFy\Form\Forms\FormBaseController;
use tiFy\Plugins\Shop\Contracts\AddressesInterface;
use tiFy\Plugins\Shop\Contracts\AddressInterface;
use tiFy\Plugins\Shop\Contracts\UserItemInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

abstract class AbstractAddress implements AddressInterface
{
    use ShopResolverTrait;

    /**
     * Identifiant de qualification.
     * @var string
     */
    protected $id = '';

    /**
     * Instance de la classe de gestion des adresses.
     * @var AddressesInterface
     */
    protected $addresses;

    /**
     * Instance de la classe de gestion du formulaire.
     * @var FormBaseController
     */
    protected $form;

    /**
     * Instance de la classe de l'utilisateur courant.
     * @var UserItemInterface
     */
    protected $user;

    /**
     * CONSTRUCTEUR.
     *
     * @param AddressesInterface $address Instance de la classe de gestion des adresses.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(AddressesInterface $addresses, Shop $shop)
    {
        $this->shop = $shop;
        $this->addresses = $addresses;

        $this->app()->appAddAction(
            'tify_form_register',
            function ($formController) {
                $this->user = $this->shop->users()->get();

                /**
                 * Traitement de la liste des champs.
                 * {@internal Ajout du préfixe aux identifiants de champ et récupération de la valeur.}
                 */
                $attrs = $this->formAttrs();
                foreach ($attrs['fields'] as $slug => &$fattrs) :
                    if (!isset($fattrs['slug'])) :
                        $fattrs['slug'] = $this->getId() . '_' . $slug;
                    endif;

                    if (!isset($fattrs['value'])) :
                        $method = 'get' . $this->getId() . Str::studly($slug);
                        $fattrs['value'] = $this->shop->session()->get($this->getId() . '.' . $slug)
                            ?: (method_exists($this->user, $method)
                                ? call_user_func([$this->user, $method])
                                : ''
                            );
                    endif;
                endforeach;

                $attrs['addons']['tify_shop_address_form_handler'] = ['controller' => $this];

                /** @var Form $formController */
                if (
                    $form = $formController->register(
                        '_tiFyShop-formAddress--' . $this->getId(),
                        $attrs
                    )
                ) :
                    $this->form = $form;
                endif;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addons()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function buttons()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function callbacks()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields = [];
        $defaults = $this->addresses->defaultFields();

        foreach ($defaults as $slug => $attrs) :
            $fields[$slug] = $attrs;
        endforeach;

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function form()
    {
        if ($this->form instanceof FormBaseController) :
            return $this->form->display();
        else :
            return '';
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function formAttrs()
    {
        return [
            /// Identifiant HTML du conteneur
            'container_id'    => sprintf('tiFyShop-addressFormContainer--%s', $this->getId()),
            /// Classe HTML du conteneur
            'container_class' => '',
            /// Identifiant HTML de la balise form
            'form_id'         => sprintf('tiFyShop-addressForm--%s', $this->getId()),
            /// Classe HTML de la balise form
            'form_class'      => '',
            /// Pré-affichage avant la balise form
            'before'          => '',
            /// Post-affichage après la balise form
            'after'           => '',
            // Attributs HTML de la balise form
            'method'          => 'post',
            'action'          => '',
            'enctype'         => '',
            // Attributs de paramètrage
            'addons'          => $this->addons(),
            'buttons'         => $this->buttons(),
            'fields'          => $this->fields(),
            'notices'         => $this->notices(),
            'options'         => $this->options(),
            'callbacks'       => $this->callbacks()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id ?: strtolower($this->appShortname());
    }

    /**
     * {@inheritdoc}
     */
    public function notices()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function options()
    {
        return [];
    }
}