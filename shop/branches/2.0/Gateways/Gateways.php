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

use Illuminate\Support\Arr;
use LogicException;
use tiFy\Apps\AppController;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\Gateways\CashOnDeliveryGateway\CashOnDeliveryGateway;
use tiFy\Plugins\Shop\Gateways\ChequeGateway\ChequeGateway;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;

class Gateways extends AppController implements GatewaysInterface, ProvideTraitsInterface
{
    use ProvideTraits;

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
     * Liste des plateformes par défaut.
     * @var array
     */
    protected $defaults = [
        'cash_on_delivery' => CashOnDeliveryGateway::class,
        'cheque'           => ChequeGateway::class
    ];

    /**
     * Liste des plateformes déclarées.
     * @var array
     */
    protected $registered = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    protected function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Définition de la liste des événement
        $this->appAddAction('after_setup_tify');
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
     * Instanciation de la classe.
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

        self::$instance = new self($shop);

        if(! self::$instance instanceof Gateways) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge doit hériter de %s', 'tify'),
                    Gateways::class
                ),
                500
            );
        endif;

        return self::$instance;
    }

    /**
     * A l'issue de l'initialisation de PresstiFy.
     *
     * @return void
     */
    final public function after_setup_tify()
    {
        // Définition de la liste des plateformes déclarées
        $this->register();
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

        foreach($this->defaults as $id => $controller) :
            $this->add($id, $controller);
        endforeach;

        $this->appEmit('tify.plugins.shop.gateways.register', $this);

        $items = [];
        if ($this->registered) :
            $gateways = $this->config('gateways', []);

            foreach($this->registered as $id => $class_name) :
                $config = Arr::get($gateways, $id, []);
                if ($config === false) :
                    $attrs = ['enabled' => false];
                elseif ($attrs = (array)Arr::get($gateways, $id, [])) :
                    $attrs['enabled'] = isset($attrs['enabled']) ? (bool) $attrs['enabled'] : true;
                elseif (in_array($id, $gateways)) :
                    $attrs = ['enabled' => true];
                else :
                    $attrs = ['enabled' => false];
                endif;

                $items[$id] = $this->provide(
                    "gateways.{$id}",
                    [
                        $id,
                        $attrs,
                        $this->shop
                    ]
                );
            endforeach;
        endif;

        return $this->list_controller = new GatewayList($items, $this->shop);
    }

    /**
     * Ajout d'une déclaration de plateforme de paiement.
     *
     * @param string $id Identifiant de qualification de la plateforme de paiement.
     * @param string $class_name Nom de la classe de rappel de traitement de la plateforme.
     *
     * @return void
     */
    final public function add($id, $class_name)
    {
        if (! isset($this->registered[$id]) && class_exists($class_name)) :
            $this->registered[$id] = $class_name;

            $this->provider()->add("gateways.{$id}", $class_name);
        endif;
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