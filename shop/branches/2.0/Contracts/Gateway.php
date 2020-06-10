<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use Psr\Log\LoggerInterface as Logger;
use tiFy\Contracts\Support\ParamsBag;

interface Gateway extends ParamsBag, ShopAwareTrait
{
    /**
     * Formulaire de paiement de la commande.
     *
     * @return void
     */
    public function checkoutPaymentForm(): void;

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Récupération de l'image d'identification de la plateforme.
     *
     * @return string
     */
    public function getIcon(): string;

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Récupération de la description spécifique à l'interface d'administration.
     *
     * @return string
     */
    public function getMethodDescription(): string;

    /**
     * Récupération de l'intitulé de qualification spécifique à l'interface d'administration.
     *
     * @return string
     */
    public function getMethodTitle(): string;

    /**
     * Récupération du texte du bouton de paiement.
     *
     * @return string
     */
    public function getOrderButtonText(): string;

    /**
     * Url de retour (Page de remerciement).
     *
     * @param Order|null $order Classe de rappel de la commande.
     *
     * @return string
     */
    public function getReturnUrl(?Order $order = null): string;

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Vérifie si la plateforme contient des champs de soumission au moment du paiement de la commande.
     *
     * @return boolean
     */
    public function hasFields(): bool;

    /**
     * Affichage de l'image d'identification de la plateforme.
     *
     * @return string
     */
    public function icon(): string;

    /**
     * Vérifie si une plateforme de paiement est disponible.
     *
     * @return boolean
     */
    public function isAvailable(): bool;

    /**
     * Vérifie si la plateforme a été choisie en tant que méthode de paiement de la commande.
     *
     * @return boolean
     */
    public function isChoosen(): bool;

    /**
     * Vérifie si une plateforme de paiement est active.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Journalisation des événements|Récupération de l'instance du gestionnaire de journalisation.
     *
     * @param int|string|null $level Niveau de notification
     * @param string $message Intitulé du message du journal.
     * @param array $context Liste des éléments de contexte.
     *
     * @return Logger|null
     */
    public function logger($level = null, string $message = '', array $context = []): ?Logger;

    /**
     * Procède au paiement de la commande.
     *
     * @param Order $order Classe de rappel de la commande à régler.
     *
     * @return array {
     *      Liste des attributs de retour.
     *
     *      @var string $result Résultat de paiement success|error.
     *      @var string $redirect Url de retour
     * }
     */
    public function processPayment(Order $order): array;

    /**
     * Définition du statu d'activation de la plateforme.
     *
     * @param boolean $enabled
     *
     * @return static
     */
    public function setEnabled(bool $enabled): Gateway;

    /**
     * Définition de l'instance du gestionnaire de journalisation.
     *
     * @param Logger $logger
     *
     * @return static
     */
    public function setLogger(Logger $logger): Gateway;
}