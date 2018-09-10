<?php

/**
 * @name Gateways
 * @desc Gestion des plateformes de paiement.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Gateways;

use Illuminate\Support\Arr;
use LogicException;
use tiFy\Plugins\Shop\Gateways\CashOnDeliveryGateway\CashOnDeliveryGateway;
use tiFy\Plugins\Shop\Gateways\ChequeGateway\ChequeGateway;
use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\GatewayInterface;
use tiFy\Plugins\Shop\Contracts\GatewayListInterface;
use tiFy\Plugins\Shop\Contracts\GatewaysInterface;

class Gateways extends AbstractShopSingleton implements GatewaysInterface
{
    /**
     * Instance du controleur de gestion de la liste des plateformes de paiement déclarées.
     * @var GatewayListInterface
     */
    protected $list;

    /**
     * Liste des identifiants de qualification des plateformes déclarées.
     * @var string[]
     */
    protected $registered = [
        'shop.gateways.cash_on_delivery',
        'shop.gateways.cheque'
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'after_setup_tify',
            function() {
                $this->_register();
            }
        );
    }

    /**
     * Définition de la liste des plateformes de paiement déclarées.
     *
     * @return GatewayListInterface
     */
    private function _register()
    {
        app()->appEventTrigger('tify.plugins.shop.gateways.register', $this);

        $gateways = [];
        foreach($this->config("gateways", []) as $id => $attrs) :
            if (is_numeric($id)) :
                $id = $attrs;
                $attrs = [];
            endif;

            if ($attrs === false) :
                $attrs = ['enabled' => false];
            elseif (!isset($attrs['enabled'])) :
                $attrs['enabled'] = true;
            endif;

            $gateways[$id] = app(
                "shop.gateway.{$id}",
                [
                    $id,
                    $attrs,
                    $this->shop
                ]
            );
        endforeach;

        return $this->list = app('shop.gateways.list', [$gateways, $this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function add($id, $concrete)
    {
        $alias = "shop.gateway.{$id}";

        if (!in_array($alias, $this->registered)) :
            app()->singleton($alias, $concrete);
            array_push($this->registered, $alias);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->list_controller->all();
    }

    /**
     * {@inheritdoc}
     */
    public function available()
    {
        return $this->list->available();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->list->get($id);
    }
}