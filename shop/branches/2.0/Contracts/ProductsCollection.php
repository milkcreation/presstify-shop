<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\Collection;

interface ProductsCollection extends Collection, ShopAwareTrait
{
    /**
     * Récupération de la liste des produits mis en avant.
     *
     * @return Product[]|array
     */
    public function featured(): array;
}