<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface GatewayInterface extends ParamsBag
{
    /**
     * Formulaire de paiement de la commande.
     *
     * @return void
     */
    public function checkoutPaymentForm();

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Récupération de l'image d'identification de la plateforme.
     *
     * @return string
     */
    public function getIcon();

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @return string
     */
    public function getId();

    /**
     * Récupération de la description spécifique à l'interface d'administration.
     *
     * @return string
     */
    public function getMethodDescription();

    /**
     * Récupération de l'intitulé de qualification spécifique à l'interface d'administration.
     *
     * @return string
     */
    public function getMethodTitle();

    /**
     * @return string
     */
    public function getOrderButtonText();

    /**
     * Url de retour (Page de remerciement).
     *
     * @param null|OrderInterface $order Classe de rappel de la commande.
     *
     * @return string
     */
    public function getReturnUrl($order = null);

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Vérifie si la plateforme contient des champs de soumission au moment du paiement de la commande.
     *
     * @return bool
     */
    public function hasFields();

    /**
     * Affichage de l'image d'identification de la plateforme.
     *
     * @return string
     */
    public function icon();

    /**
     * Vérifie si une plateforme de paiement est disponible.
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * Vérifie si la plateforme a été choisie en tant que méthode de paiement de la commande.
     *
     * @return bool
     */
    public function isChoosen();

    /**
     * Vérifie si une plateforme de paiement est active.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Journalisation des actions.
     *
     * @param string $message Message
     * @param string $type Type de notification. DEBUG|INFO (par défaut)|NOTICE|WARNING|ERROR|CRITICAL|ALERT|EMERGENCY.
     * @param array $context Données complémentaire de contexte
     *
     * @return void
     */
    public function log($message, $type = 'INFO', $context = []);

    /**
     * Procède au paiement de la commande.
     *
     * @param OrderInterface $order Classe de rappel de la commande à régler.
     *
     * @return array {
     *      Liste des attributs de retour.
     *
     * @var string $result Résultat de paiement success|error.
     * @var string $redirect Url de retour
     * }
     */
    public function processPayment(OrderInterface $order);
}