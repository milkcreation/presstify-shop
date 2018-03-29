<?php

namespace tiFy\Plugins\Shop\Gateways;

use Illuminate\Support\Fluent;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Orders\OrderInterface;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

abstract class AbstractGateway extends Fluent implements GatewayInterface, ProvideTraitsInterface
{
    use TraitsApp, ProvideTraits;

    /**
     * Identifiant de qualification de la plateforme
     * @var string
     */
    protected $id = '';

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Activation du mode de déboguage
     * @internal Journalisation des processus engagés, affichage des information de deboguage, ...
     * @var bool
     */
    protected $debug = false;

    /**
     * Définition des attributs par défaut de la plateforme
     *
     * @var array
     */
    protected $defaults = [
        'id'                   => '',
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
        'debug'                => false
    ];

    /**
     * CONSTRUCTEUR
     *
     * @param int $id Identifiant de qualification de la plateforme.
     * @param array $attributes Liste des attributs de configuration de la plateforme.
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    public function __construct($id, $attributes = [], Shop $shop)
    {
        $this->id = $id;

        parent::__construct(
            array_merge(
                $this->getDefaults(),
                $attributes
            )
        );

        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;
    }

    /**
     * Récupération de l'identifiant de qualification
     *
     * @return string
     */
    public function getId()
    {
        return $this->id ? : $this->appLowerName();
    }

    /**
     * Récupération des attributs de configuration par défaut
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @return string
     */
    public function getOrderButtonText()
    {
        return $this->get('order_button_text', '');
    }

    /**
     * Vérifie si une plateforme de paiement est active.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->get('enabled', true);
    }

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->get('title', '');
    }

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->get('description', '');
    }

    /**
     * Récupération de l'intitulé de qualification spécifique à l'interface d'administration.
     *
     * @return string
     */
    public function getMethodTitle()
    {
        return $this->get('method_title', '');
    }

    /**
     * Récupération de la description spécifique à l'interface d'administration.
     *
     * @return string
     */
    public function getMethodDescription()
    {
        return $this->get('method_description', '');
    }

    /**
     * Vérifie si la plateforme contient des champs de soumission au moment du paiement de la commande.
     *
     * @return bool
     */
    public function hasFields()
    {
        return $this->get('has_fields', false);
    }

    /**
     * Récupération de l'image d'identification de la plateforme.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->get('icon', '');
    }

    /**
     * Affichage de l'image d'identification de la plateforme.
     *
     * @return string
     */
    public function icon()
    {
        return  $this->getIcon() ? '<img src="' . $this->getIcon() . '" alt="' . esc_attr($this->getTitle()) . '" />' : '';
    }

    /**
     * Vérifie si une plateforme de paiement est disponible.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->isEnabled();
    }

    /**
     * Vérifie si la plateforme a été choisie en tant que méthode de paiement de la commande.
     *
     * @return bool
     */
    public function isChoosen()
    {
        return $this->get('choosen', true);
    }

    /**
     * Url de retour (Page de remerciement).
     *
     * @param null|OrderInterface $order Classe de rappel de la commande.
     *
     * @return string
     */
    public function getReturnUrl($order = null)
    {
        if ($order) :
            $order->getCheckoutOrderReceivedUrl();
        else :
            return $this->functions()->url()->checkoutOrderReceivedPage([
                'order-received' => ''
            ]);
        endif;
    }

    /**
     * Procède au paiement de la commande.
     *
     * @param OrderInterface $order Classe de rappel de la commande à régler.
     *
     * @return array {
     *      Liste des attributs de retour.
     *
     *      @var string $result Résultat de paiement success|error.
     *      @var string $redirect Url de retour
     * }
     */
    public function processPayment($order)
    {
        return [];
    }

    /**
     * Formulaire de paiement de la commande.
     *
     * @return void
     */
    public function checkoutPaymentForm()
    {
        echo '';
    }

    /**
     * Journalisation des actions.
     *
     * @param string $message Message
     * @param string $type Type de notification. DEBUG|INFO (par défaut)|NOTICE|WARNING|ERROR|CRITICAL|ALERT|EMERGENCY.
     *
     * @return void
     */
    public function log($message, $type = 'INFO')
    {
        if (! $this->get('debug', false)) :
            return;
        endif;

        $Type = strtoupper($type);
        if (! in_array($Type, ['DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])) :
            return;
        endif;

        $logger = $this->appLog();
        $levels = $logger::getLevels();

        $logger->addRecord($levels[$Type], $message);
    }
}