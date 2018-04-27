<?php

namespace tiFy\Plugins\Shop\Users;

class LoggedOut implements LoggedOutInterface
{
    /**
     * Récupération de l'identifiant de qualification Wordpress de l'utilisateur
     *
     * @return int
     */
    public function getId()
    {
        return 0;
    }

    /**
     * Récupération de l'identifiant de connection de l'utilisateur
     *
     * @return string
     */
    public function getLogin()
    {
        return '';
    }

    /**
     * Récupération du mot de passe encrypté
     *
     * @return string
     */
    public function getPass()
    {
        return '';
    }

    /**
     * Récupération du surnom
     *
     * @return string
     */
    public function getNicename()
    {
        return '';
    }

    /**
     * Récupération de l'email
     *
     * @return string
     */
    public function getEmail()
    {
        return '';
    }

    /**
     * Récupération de l'url du site internet associé à l'utilisateur
     *
     * @return string
     */
    public function getUrl()
    {
        return '';
    }

    /**
     * Récupération de la date de création du compte utilisateur
     *
     * @return string
     */
    public function getRegistered()
    {
        return '';
    }

    /**
     * Récupération du nom d'affichage public
     *
     * @return string
     */
    public function getDisplayName()
    {
        return '';
    }

    /**
     * Récupération du prénom
     *
     * @return string
     */
    public function getFirstName()
    {
        return '';
    }

    /**
     * Récupération du nom de famille
     *
     * @return string
     */
    public function getLastName()
    {
        return '';
    }

    /**
     * Récupération du pseudonyme
     *
     * @return string
     */
    public function getNickname()
    {
        return '';
    }

    /**
     * Récupération des renseignements biographiques
     *
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * Vérifie si l'utilisateur est connecté
     *
     * @bool
     */
    public function isLoggedIn()
    {
        return false;
    }

    /**
     * Récupération de la liste des roles
     * @return array
     */
    public function getRoles()
    {
        return [];
    }

    /**
     * Vérification de l'appartenance à un role
     *
     * @param string $role Identifiant de qualification du rôle
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return false;
    }

    /**
     * Vérifie si un utilisateur est considéré en tant que client
     *
     * @return bool
     */
    public function isCustomer()
    {
        return false;
    }

    /**
     * Vérifie si un utilisateur est considéré en tant que client
     *
     * @return bool
     */
    public function isShopManager()
    {
        return false;
    }

    /**
     * Récupération du prénom de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingFirstName()
    {
        return '';
    }

    /**
     * Récupération du nom de famille de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingLastName()
    {
        return '';
    }

    /**
     * Récupération du nom de famille de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingCompany()
    {
        return '';
    }

    /**
     * Récupération de la ligne principale de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingAddress1()
    {
        return '';
    }

    /**
     * Récupération de la ligne complémentaire de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingAddress2()
    {
        return '';
    }

    /**
     * Récupération de la ville de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingCity()
    {
        return '';
    }

    /**
     * Récupération du code postal de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingPostcode()
    {
        return '';
    }

    /**
     * Récupération du pays de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingCountry()
    {
        return '';
    }

    /**
     * Récupération du numéro de téléphone de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingPhone()
    {
        return '';
    }

    /**
     * Récupération de l'email de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingEmail()
    {
        return '';
    }

    /**
     * Vérification des habilitations.
     *
     * @see WP_User::has_cap()
     * @see map_meta_cap()
     *
     * @param string $capability Nom de qalification de l'habiltation.
     * @param int $object_id  Optionel. Identifiant de qualification de l'object à vérifier lorsque $capability est de type "meta".
     *
     * @return bool
     */
    public function can($capability)
    {
        return false;
    }
}