<?php

/**
 * @name Addresses
 * @desc Gestion des adresses : livraison|facturation|pays
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Address
 * @version 1.1
 * @since 1.2.600
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Addresses;

use LogicException;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Core\Forms\Addons;
use tiFy\Plugins\Shop\Shop;

class Addresses implements AddressesInterface
{
    use TraitsApp;

    /**
     * Instance de la classe
     * @var Addresses
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel de gestion de l'adresse de facturation
     * @var BillingInterface
     */
    protected $billing;

    /**
     * Classe de rappel de gestion de l'adresse de livraison
     * @var ShippingInterface
     */
    protected $shipping;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    protected function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Déclaration des événements
        $this->appAddAction('tify_form_register_addon');
    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    protected function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    protected function __wakeup()
    {

    }

    /**
     * Instanciation de la classe
     *
     * @param Shop $shop
     *
     * @return Addresses
     */
    public static function boot(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * Déclaration de l'addon de traitement des formulaire
     *
     * @return void
     */
    final public function tify_form_register_addon()
    {
        $this->billing();
        $this->shipping();

        $form_handler_class = $this->shop->provider()->getMapController('addresses.form_handler');
        if(! in_array(FormHandlerInterface::class, class_implements($form_handler_class))) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge doit implémenter %s', 'tify'),
                    FormHandlerInterface::class
                ),
                500
            );
        endif;

        Addons::register(
            'tify_shop_address_form_handler',
            $form_handler_class,
            $this->shop
        );
    }

    /**
     * Récupération du controleur de gestion de l'adresse de facturation
     *
     * @return BillingInterface
     *
     * @throw LogicException
     */
    final public function billing()
    {
        if ($this->billing) :
            return $this->billing;
        endif;

        $this->billing = $this->shop->provide('addresses.billing');
        if(! $this->billing instanceof BillingInterface) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge doit implémenter %s', 'tify'),
                    BillingInterface::class
                ),
                500
            );
        endif;

        return $this->billing;
    }

    /**
     * Récupération du controleur de gestion de l'adresse de livraison
     *
     * @return ShippingInterface
     */
    final public function shipping()
    {
        if ($this->shipping) :
            return $this->shipping;
        endif;

        $this->shipping = $this->shop->provide('addresses.shipping');
        if(! $this->shipping instanceof ShippingInterface) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge doit implémenter %s', 'tify'),
                    ShippingInterface::class
                ),
                500
            );
        endif;

        return $this->shipping;
    }

    /**
     * Définition des champs de formulaire par défaut
     *
     * @return array
     */
    public function defaultFields()
    {
        return [
            'first_name' => [
                'label'        => __('Prénom', 'tify'),
                'required'     => true,
                'autocomplete' => 'given-name',
                'order'        => 10,
            ],
            'last_name'  => [
                'label'        => __('Nom de famille', 'tify'),
                'required'     => true,
                'autocomplete' => 'family-name',
                'order'        => 20,
            ],
            'company'    => [
                'label'        => __('Société', 'tify'),
                'autocomplete' => 'organization',
                'order'        => 30,
            ],
            'country'    => [
                'label'        => __('Pays', 'tify'),
                'required'     => true,
                'class'        => ['form-row-wide', 'address-field', 'update_totals_on_change'],
                'autocomplete' => 'country',
                'order'        => 40,
            ],
            'address_1'  => [
                'label'        => __('Numéro et nom de rue', 'tify'),
                'placeholder'  => esc_attr__('N° et rue', 'tify'),
                'required'     => true,
                'autocomplete' => 'address-line1',
                'order'        => 50,
            ],
            'address_2'  => [
                'label'        => __('Complément d\'adresse', 'tify'),
                'placeholder'  => esc_attr__('Appartement, porte, bureau, etc. (optionnel)', 'tify'),
                'required'     => false,
                'autocomplete' => 'address-line2',
                'order'        => 60,
            ],
            'city'       => [
                'label'        => __('Ville', 'tify'),
                'required'     => true,
                'autocomplete' => 'address-level2',
                'order'        => 70,
            ],
            'state'      => [
                'label'        => __('Département / Région', 'tify'),
                'required'     => true,
                'autocomplete' => 'address-level1',
                'order'        => 80,
            ],
            'postcode'   => [
                'label'        => __('Code postal', 'tify'),
                'required'     => true,
                'autocomplete' => 'postal-code',
                'order'        => 90,
            ]
        ];
    }
}