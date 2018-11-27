<?php

namespace tiFy\Plugins\Shop\Gateways;

use Illuminate\Support\Str;
use tiFy\Contracts\Kernel\Logger;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Shop\Contracts\GatewayInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

abstract class AbstractGateway extends ParamsBag implements GatewayInterface
{
    use ShopResolverTrait;

    /**
     * Identifiant de qualification de la plateforme.
     * @var string
     */
    protected $id = '';

    /**
     * Activation du mode de déboguage.
     * {@internal Journalisation des processus engagés, affichage des information de deboguage, ...}
     * @var bool
     */
    protected $debug = false;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $id Identifiant de qualification de la plateforme.
     * @param array $attrs Liste des attributs de configuration de la plateforme.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct($id, $attrs = [], Shop $shop)
    {
        $this->id = $id;
        $this->shop = $shop;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function checkoutPaymentForm()
    {
        echo '';
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'order_button_text'    => '',
            'enabled'              => true,
            'title'                => '',
            'description'          => '',
            'method_title'         => '',
            'method_description'   => '',
            'has_fields'           => false,
            'countries'            => [],
            'availability'         => '',
            'icon'                 => '',
            'choosen'              => false,
            'supports'             => ['products'],
            'max_amount'           => 0,
            'view_transaction_url' => '',
            'tokens'               => [],
            'debug'                => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->get('description', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return $this->get('icon', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodDescription()
    {
        return $this->get('method_description', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodTitle()
    {
        return $this->get('method_title', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderButtonText()
    {
        return $this->get('order_button_text', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnUrl($order = null)
    {
        if ($order) :
            return $order->getCheckoutOrderReceivedUrl();
        else :
            return $this->functions()->url()->checkoutOrderReceivedPage([
                'order-received' => '',
            ]);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->get('title', '');
    }

    /**
     * {@inheritdoc}
     */
    public function hasFields()
    {
        return $this->get('has_fields', false);
    }

    /**
     * {@inheritdoc}
     */
    public function icon()
    {
        return $this->getIcon()
            ? '<img src="' . $this->getIcon() . '" alt="' . esc_attr($this->getTitle()) . '" />'
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return $this->isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function isChoosen()
    {
        return $this->get('choosen', true);
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->get('enabled', true);
    }

    /**
     * {@inheritdoc}
     */
    public function log($message, $type = 'INFO', $context = [])
    {
        if (!$this->get('debug', false)) :
            return;
        endif;

        $Type = Str::upper($type);
        if (!in_array($Type, ['DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])) :
            return;
        endif;
        /** @var Logger $logger */
        $logger = app()->bound("shop.gateways.logger.{$this->getId()}")
            ? app("shop.gateways.logger.{$this->getId()}")
            : app()->singleton(
                "shop.gateways.logger.{$this->getId()}",
                function () {
                    return app('logger', ["shop.gateways.{$this->getId()}"]);
                }
            )->build();

        $levels = $logger::getLevels();

        $logger->log($levels[$Type], $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function processPayment($order)
    {
        return [];
    }
}