<?php

namespace tiFy\Plugins\Shop\Gateways;

use tiFy\Plugins\Shop\Shop;

interface GatewaysInterface
{
    /**
     * Instanciation de la classe.
     * @param Shop $shop
     * @return self
     */
     public static function boot(Shop $shop);

    /**
     * Ajout d'une déclaration de plateforme de paiement.
     * @param string $name Identifiant de qualification de la plateforme de paiement.
     * @param string $class_name Nom de la classe de rappel de traitement de la plateforme.
     * @return void
     */
    public function add($name, $class_name);

    /**
     * Récupération de la liste complète des plateforme de paiement déclarées
     * @return GatewayInterface[]
     */
    public function all();

    /**
     * Récupération de la liste des plateformes de paiement disponibles
     * @return GatewayInterface[]
     */
    public function available();

    /**
     * Récupération de la liste complète des plateforme de paiement déclarées
     * @param string Identifiant de qualification de la plateforme de paiement
     * @return null|GatewayInterface
     */
    public function get($id);
}