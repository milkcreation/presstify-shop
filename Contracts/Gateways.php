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
     * Initialisation.
     *
     * @return static
     */
    public function boot(): Gateways;

    /**
     * {@inheritDoc}
     *
     * @param string $id
     *
     * @return Gateway|null
     */
    public function get($id): ?Gateway;
}