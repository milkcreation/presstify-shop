<?php

namespace tiFy\Plugins\Shop\Addresses;

use Illuminate\Support\Str;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Core\Forms\Forms;
use tiFy\Plugins\Shop\Shop;

abstract class AbstractAddress implements AddressInterface
{
    use TraitsApp;

    /**
     * Identifiant de qualification
     * @var string
     */
    protected $id = '';

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel de gestion des adresses
     * @var AddressesInterface
     */
    protected $addresses;

    /**
     * @var \tiFy\Plugins\Shop\Users\UserInterface
     */
    protected $user;

    /**
     * Classe de rappel du formulaire
     * @var \tiFy\Core\Forms\Factory
     */
    protected $form;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     * @param AddressesInterface $address Classe de rappel de gestion des adresses
     *
     * @return void
     */
    public function __construct(Shop $shop, AddressesInterface $addresses)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Définition de la classe de rappel de gestion des adresses
        $this->addresses = $addresses;

        // Déclaration des événements
        $this->appAddAction('tify_form_register');
    }

    /**
     * Déclaration du formulaire
     *
     * @return void
     */
    final public function tify_form_register()
    {
        $this->user = $this->shop->users()->get();

        $attrs = $this->formAttrs();

        /**
         * Traitement de la liste des champs
         * @internal Ajout du préfixe aux identifiants de champ et récupération de la valeur
         */
        foreach ($attrs['fields'] as $slug => &$fattrs) :
            if (!isset($fattrs['slug'])) :
                $fattrs['slug'] = $this->getId() . '_' . $slug;
            endif;
            if (!isset($fattrs['value'])) :
                $method = 'get' . $this->getId() . Str::studly($slug);
                $fattrs['value'] = $this->shop->session()->get($this->getId() . '.' . $slug)
                    ?: (method_exists($this->user, $method) ? call_user_func([$this->user, $method]) : '');
            endif;
        endforeach;

        $attrs['addons']['tify_shop_address_form_handler'] = ['controller' => $this];

        if (
        $form_id = Forms::register(
            '_tiFyShop-formAddress--' . $this->getId(),
            $attrs
        )
        ) :
            $this->form = Forms::get($form_id);
        endif;
    }

    /**
     * Récupération de l'identifiant de qualification
     *
     * @return string
     */
    public function getId()
    {
        return $this->id ?: strtolower($this->appShortname());
    }

    /**
     * Définition des attributs de configuration du formulaire
     * @see \tiFy\Core\Forms\Form\Form
     *
     * @return array
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
     * Définition de la liste des addons de formulaire
     *
     * @return array
     */
    public function addons()
    {
        return [];
    }

    /**
     * Définition de la liste des boutons d'action du formulaire
     *
     * @return array
     */
    public function buttons()
    {
        return [];
    }

    /**
     * Définition de la liste des champs de formulaire
     * @see \tiFy\Core\Forms\Form\Field
     *
     * @return array
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
     * Définition de la liste des messages de notification du formulaire
     *
     * @return array
     */
    public function notices()
    {
        return [];
    }

    /**
     * Définition de la liste des options du formulaire
     *
     * @return array
     */
    public function options()
    {
        return [];
    }

    /**
     * Définition de la liste des événements de déclenchement
     *
     * @return array
     */
    public function callbacks()
    {
        return [];
    }

    /**
     * Récupération du formulaire de traitement de l'adresse de livraison
     *
     * @return string
     */
    public function form()
    {
        if ($this->form instanceof \tiFy\Core\Forms\Factory) :
            return $this->form->display();
        else :
            return '';
        endif;
    }
}