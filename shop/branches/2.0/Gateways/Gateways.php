<?php

namespace tiFy\Plugins\Shop\Gateways;

use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\GatewayListInterface;
use tiFy\Plugins\Shop\Contracts\GatewaysInterface;

/**
 * Class Gateways
 *
 * @desc Gestion des plateformes de paiement.
 */
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
        $this->_register();
    }

    /**
     * Définition de la liste des plateformes de paiement déclarées.
     *
     * @return GatewayListInterface
     */
    private function _register()
    {
        events()->trigger('tify.plugins.shop.gateways.register', [&$this]);

        $gateways = [];
        foreach($this->config("gateways", []) as $id => $attrs) :
            if (is_numeric($id)) :
                $id = $attrs;
                $attrs = [];
            endif;

            if (is_callable($attrs)) :
                $gateways[$id] = call_user_func_array($attrs, [$id, [], $this->shop]);
            else :
                if ($attrs === false) :
                    $attrs = ['enabled' => false];
                elseif (!isset($attrs['enabled'])) :
                    $attrs['enabled'] = true;
                endif;

                $gateways[$id] = app("shop.gateway.{$id}", [$id, $attrs, $this->shop]);
            endif;
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
            $concrete = $this->provider()->getConcrete($alias);
            app()->add($alias, $concrete);
            array_push($this->registered, $alias);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->list->all();
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