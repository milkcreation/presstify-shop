<?php

namespace tiFy\Plugins\Shop\Contracts;

interface AddressInterface extends ShopResolverInterface
{
    /**
     * Définition de la liste des addons de formulaire.
     *
     * @return array
     */
    public function addons();

    /**
     * Définition de la liste des boutons d'action du formulaire.
     *
     * @return array
     */
    public function buttons();

    /**
     * Définition de la liste des événements de déclenchement.
     *
     * @return array
     */
    public function callbacks();

    /**
     * Définition de la liste des champs de formulaire.
     *
     * @return array
     */
    public function fields();

    /**
     * Récupération du formulaire de traitement de l'adresse de livraison.
     *
     * @return string
     */
    public function form();

    /**
     * Définition des attributs de configuration du formulaire.
     * @see \tiFy\Form\Controller\Form
     *
     * @return array
     */
    public function formAttrs();

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @return string
     */
    public function getId();

    /**
     * Définition de la liste des messages de notification du formulaire.
     *
     * @return array
     */
    public function notices();

    /**
     * Définition de la liste des options du formulaire.
     *
     * @return array
     */
    public function options();
}