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
use tiFy\Apps\AppController;
use tiFy\Form\Addons\AddonsController;
use tiFy\Plugins\Shop\Shop;

class Addresses extends AppController implements AddressesInterface
{
    /**
     * Instance de la classe
     * @var AddressesInterface
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
        $this->shop = $shop;

        $this->appAddAction('tify_form_addon_register');
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Instanciation de la classe
     *
     * @param Shop $shop
     *
     * @return AddressesInterface
     */
    public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        self::$instance = new self($shop);

        if(! self::$instance instanceof AddressesInterface) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge devrait être une instance de %s', 'tify'),
                    AddressesInterface::class
                ),
                500
            );
        endif;

        return self::$instance;
    }

    /**
     * Déclaration de l'addon de traitement des formulaire.
     *
     * @param AddonsController $addonsController Classe de rappel de gestion des addons de formulaire.
     *
     * @return void
     */
    final public function tify_form_addon_register($addonsController)
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

        $addonsController->register(
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
                'title'        => __('Prénom', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'given-name',
                ],
                'order'        => 10,
            ],
            'last_name'  => [
                'title'        => __('Nom de famille', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'family-name',
                ],
                'order'        => 20,
            ],
            'company'    => [
                'title'        => __('Société', 'tify'),
                'type'         => 'text',
                'attrs'        => [
                    'autocomplete' => 'organization',
                ],
                'order'        => 30,
            ],
            'country'    => [
                'title'        => __('Pays', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'country',
                ],
                'order'        => 40,
            ],
            'address_1'  => [
                'title'        => __('Numéro et nom de rue', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'address-line1',
                    'placeholder'  => esc_attr__('N° et rue', 'tify'),
                ],
                'order'        => 50,
            ],
            'address_2'  => [
                'title'        => __('Complément d\'adresse', 'tify'),
                'type'         => 'text',
                'attrs'        => [
                    'autocomplete' => 'address-line2',
                    'placeholder'  => esc_attr__('Appartement, porte, bureau, etc. (optionnel)', 'tify'),
                ],
                'order'        => 60,
            ],
            'city'       => [
                'title'        => __('Ville', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'address-level2',
                ],
                'order'        => 70,
            ],
            'state'      => [
                'title'        => __('Département / Région', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'address-level1',
                ],
                'order'        => 80,
            ],
            'postcode'   => [
                'title'        => __('Code postal', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'postal-code',
                ],
                'order'        => 90,
            ]
        ];
    }
}