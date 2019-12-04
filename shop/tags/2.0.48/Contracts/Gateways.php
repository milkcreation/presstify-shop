<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\Collection;

interface Gateways extends Collection, ShopAwareTrait
{
    /**
     * {@inheritDoc}
     *
     * @return Gateway[]|array
     */
    public function all(): array;

    /**
     * Récupération de la liste des plateformes de paiement disponibles.
     *
     * @return Gateway[]|array
     */
    public function available(): array;

    /**
     * {@inheritDoc}
     *
     * @param string $id
     *
     * @return Gateway|null
     */
    public function get($id): ?Gateway;
}