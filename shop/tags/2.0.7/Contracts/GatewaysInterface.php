<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Plugins\Shop\Contracts\BootableControllerInterface;
use tiFy\Plugins\Shop\Contracts\GatewayInterface;
use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;

interface GatewaysInterface extends BootableControllerInterface, ShopResolverInterface
{
    /**
     * Ajout d'une déclaration de plateforme de paiement.
     *
     * @param string $id Identifiant de qualification de la plateforme de paiement.
     * @param string|callable $concrete Nom de la classe ou Fonctions anonyme de résolution.
     *
     * @return void
     */
    public function add($id, $concrete);

    /**
     * Récupération de la liste complète des plateforme de paiement déclarée.
     *
     * @return GatewayInterface[]
     */
    public function all();

    /**
     * Récupération de la liste des plateformes de paiement disponibles.
     *
     * @return GatewayInterface[]
     */
    public function available();

    /**
     * Récupération de la liste complète des plateforme de paiement déclarées.
     *
     * @param string Identifiant de qualification de la plateforme de paiement.
     *
     * @return null|GatewayInterface
     */
    public function get($id);
}