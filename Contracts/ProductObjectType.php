<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface ProductObjectType extends ParamsBag, ShopAwareTrait
{
    /**
     * Résolution de sortie de la classe sous forme d'une chaîne de caractère.
     * {@internal Récupération de l'identifiant de qualification du type de post de définition du produit.}
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Récupération du type de produit par défaut
     *
     * @return string
     */
    public function getDefaultProductType(): string;

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de la liste des types de produit
     *
     * @return array
     */
    public function getProductTypes(): array;

    /**
     * Vérifie s'il s'agit d'une gamme de produit unique.
     *
     * @return boolean
     */
    public function hasCat(): bool;
}