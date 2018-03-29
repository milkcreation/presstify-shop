<?php

namespace tiFy\Plugins\Shop\Gateways;

use tiFy\Plugins\Shop\Orders\OrderInterface;

interface GatewayInterface
{
    /**
     * Récupération de l'identifiant de qualification
     * @return string
     */
    public function getId();

    /**
     * Récupération des attributs de configuration par défaut
     * @return array
     */
    public function getDefaults();

    /**
     * @return string
     */
    public function getOrderButtonText();

    /**
     * Vérifie si une plateforme de paiement est active.
     * @return bool
     */
    public function isEnabled();

    /**
     * Récupération de l'intitulé de qualification.
     * @return string
     */
    public function getTitle();

    /**
     * Récupération de l'intitulé de qualification.
     * @return string
     */
    public function getDescription();

    /**
     * Récupération de l'intitulé de qualification spécifique à l'interface d'administration.
     * @return string
     */
    public function getMethodTitle();

    /**
     * Récupération de la description spécifique à l'interface d'administration.
     */
    public function getMethodDescription();

    /**
     * Vérifie si la plateforme contient des champs de soumission au moment du paiement de la commande.
     * @return bool
     */
    public function hasFields();

    /**
     * Récupération de l'image d'identification de la plateforme.
     * @return string
     */
    public function getIcon();

    /**
     * Affichage de l'image d'identification de la plateforme.
     * @return string
     */
    public function icon();

    /**
     * Vérifie si une plateforme de paiement est disponible.
     * @return bool
     */
    public function isAvailable();

    /**
     * Vérifie si la plateforme a été choisie en tant que méthode de paiement de la commande.
     * @return bool
     */
    public function isChoosen();

    /**
     * Url de retour (Page de remerciement).
     * @param OrderInterface $order
     * @return string
     */
    public function getReturnUrl($order = null);

    /**
     * Procède au paiement de la commande.
     * @param OrderInterface $order Classe de rappel de la commande à régler.
     * @return array {
     *      Liste des attributs de retour.
     *
     *      @var string $result Résultat de paiement success|error.
     *      @var string $redirect Url de retour
     * }
     */
    public function processPayment($order);

    /**
     * Formulaire de paiement de la commande.
     * @return void
     */
    public function checkoutPaymentForm();

    /**
     * Journalisation des actions.
     *
     * @param string $message Message de notification.
     * @param string $type Type de notification. debug|info (par défaut)|notice|warning|error|critical|alert|emergency.
     *
     * @return void
     */
    public function log($message, $type);
}