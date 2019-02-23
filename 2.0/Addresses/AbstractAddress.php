<?php

namespace tiFy\Plugins\Shop\Addresses;

use Illuminate\Support\Str;
use tiFy\Contracts\Form\FormFactory;
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
     * @var FormFactory
     */
    protected $form = null;

    /**
     * Instance de la classe de l'utilisateur courant.
     * @var UserItemInterface
     */
    protected $user;

    /**
     * CONSTRUCTEUR.
     *
     * @param AddressesInterface $addresses Instance de la classe de gestion des adresses.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(AddressesInterface $addresses, Shop $shop)
    {
        $this->shop = $shop;
        $this->addresses = $addresses;

        add_action('init', function () {
            $this->user = $this->shop->users()->getItem();

            /**
             * Traitement de la liste des champs.
             * {@internal Ajout du préfixe aux identifiants de champ et récupération de la valeur.}
             */
            $attrs = $this->formAttrs();
            foreach ($attrs['fields'] as $slug => &$fattrs) :
                if (!isset($fattrs['name'])) :
                    $fattrs['name'] = $this->getId() . '_' . $slug;
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

            $attrs['addons']['shop.addresses.form_handler'] = ['controller' => $this];

            form()->register('ShopFormAddress-' . $this->getId(), $attrs);
        });
    }

    /**
     * @inheritdoc
     */
    public function addons()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function buttons()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function callbacks()
    {
        return [];
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function form()
    {
        if (is_null($this->form)) :
            return $this->form = form()->get('ShopFormAddress-' . $this->getId());
        elseif ($this->form instanceof FormFactory) :
            return $this->form;
        else :
            return '';
        endif;
    }

    /**
     * @inheritdoc
     */
    public function formAttrs()
    {
        return [
            'attrs' => [
                'id'    => 'FormShopAddress--'. $this->getId(),
                'class'    => 'FormShopAddress FormShopAddress--'. $this->getId()
            ],
            'addons'          => $this->addons(),
            'buttons'         => $this->buttons(),
            'fields'          => $this->fields(),
            'notices'         => $this->notices(),
            'options'         => $this->options(),
            'callbacks'       => $this->callbacks()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id ?: Str::lower(class_info($this)->getShortName());
    }

    /**
     * @inheritdoc
     */
    public function notices()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function options()
    {
        return [];
    }
}