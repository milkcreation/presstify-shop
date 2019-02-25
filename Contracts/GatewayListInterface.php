<?php

namespace tiFy\Plugins\Shop\Contracts;

interface GatewayListInterface
{
    /**
     * Récupération de la liste complète des plateformes de paiement déclarées.
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
     * Récupération d'une plateforme de paiement déclarée.
     *
     * @param string $id Identifiant de qualification de la plateforme.
     *
     * @return null|GatewayInterface
     */
    public function get($id);
}