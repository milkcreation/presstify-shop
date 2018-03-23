<?php

/**
 * @name Gateways
 * @desc Gestion des plateformes de paiement
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Gateways
 * @version 1.1
 * @since 1.3.1
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Gateways;

use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Shop;

class Gateways
{
    use TraitsApp;

    /**
     * Instance de la classe
     * @var Gateways
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Controller de gestion de la liste des plateformes de paiement déclarées.
     * @var GatewayListInterface
     */
    protected $list_controller;

    /**
     * Liste des plateformes déclarées par defaut.
     * @var array
     */
    protected $defaults = [
        'cash_on_delivery' => 'tiFy\Plugins\Shop\Gateways\CashOnDeliveryGateway\CashOnDeliveryGateway',
        'cheque'           => 'tiFy\Plugins\Shop\Gateways\ChequeGateway\ChequeGateway'
    ];

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    private function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Définition de la liste des plateformes déclarées
        $this->register();
    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation
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
     * @return Gateways
     */
    final public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * Définition de la liste des plateformes de paiement déclarées.
     *
     * @return GatewayListInterface
     */
    private function register()
    {
        if ($this->list_controller instanceof GatewayListInterface) :
            return $this->list_controller;
        endif;

        $gateways = array_merge(
            $this->defaults,
            $this->shop->appConfig('gateways')
        );

        $items = [];
        foreach($gateways as $id => $attrs) :
            if ($attrs === false) :
                continue;
            elseif (($attrs === true) && isset($this->defaults[$id])) :
                $controller = $this->defaults[$id];
                $attrs = [];
            elseif (is_string($attrs)) :
                $controller = $attrs;
                $attrs = [];
            else :
                $controller = isset($attrs['controller']) ? $attrs['controller'] : '';
                unset($attrs['controller']);
            endif;

            if (!$controller || !in_array('tiFy\Plugins\Shop\Gateways\GatewayInterface', class_implements($controller))) :
                continue;
            endif;

            /** @var GatewayInterface $gateway */
            $gateway = new $controller(
                $this->shop,
                array_merge(
                    ['id' => $id],
                    $attrs
                )
            );

            $items[$gateway->getId()] = $gateway;
        endforeach;

        return $this->list_controller = new GatewayList($this->shop, $items);
    }

    /**
     * Récupération de la liste complète des plateforme de paiement déclarées
     *
     * @return GatewayInterface[]
     */
    public function all()
    {
        return $this->list_controller->all();
    }

    /**
     * Récupération de la liste des plateformes de paiement disponibles
     *
     * @return GatewayInterface[]
     */
    public function available()
    {
        return $this->list_controller->available();
    }

    /**
     * Récupération de la liste complète des plateforme de paiement déclarées
     *
     * @param string Identifiant de qualification de la plateforme de paiement
     *
     * @return null|GatewayInterface
     */
    public function get($id)
    {
        return $this->list_controller->get($id);
    }
}