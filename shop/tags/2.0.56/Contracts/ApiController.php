<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface ApiController extends ShopAwareTrait
{
    /**
     * Documentation.
     *
     * @return array
     */
    public function index(): array;

    /**
     * Récupération des commandes.
     *
     * @param mixed ...$args
     *
     * @return array
     */
    public function order(...$args): array;
}