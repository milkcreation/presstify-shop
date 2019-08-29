<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\Collection;

interface GatewaysInterface extends Collection, ShopAwareTrait
{
    /**
     * {@inheritDoc}
     *
     * @return GatewayInterface[]
     */
    public function all(): array;

    /**
     * Récupération de la liste des plateformes de paiement disponibles.
     *
     * @return GatewayInterface[]
     */
    public function available(): array;

    /**
     * {@inheritDoc}
     *
     * @param string $id
     *
     * @return GatewayInterface|null
     */
    public function get($id): ?GatewayInterface;
}