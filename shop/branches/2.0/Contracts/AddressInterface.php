<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Form\FormFactory;

interface AddressInterface extends ShopAwareTrait
{
    /**
     * Définition de la liste des addons de formulaire.
     *
     * @return array
     */
    public function addons(): array;

    /**
     * Récupération de l'instance du gestionnaire d'adresses
     */
    public function addresses(): AddressesInterface;

    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Définition de la liste des boutons d'action du formulaire.
     *
     * @return array
     */
    public function buttons(): array;

    /**
     * Définition de la liste des événements de déclenchement.
     *
     * @return array
     */
    public function callbacks(): array;

    /**
     * Définition de la liste des champs de formulaire.
     *
     * @return array
     */
    public function fields(): array;

    /**
     * Récupération du formulaire de traitement de l'adresse de livraison.
     *
     * @return FormFactory|null
     */
    public function form(): ?FormFactory;

    /**
     * Définition des attributs de configuration du formulaire.
     * @see \tiFy\Form\Controller\Form
     *
     * @return array
     */
    public function formAttrs(): array;

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Définition de la liste des messages de notification du formulaire.
     *
     * @return array
     */
    public function notices(): array;

    /**
     * Définition de la liste des options du formulaire.
     *
     * @return array
     */
    public function options(): array;
}