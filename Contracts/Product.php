<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Wordpress\Contracts\Query\QueryPost;
use WP_Term;

interface Product extends QueryPost, ShopAwareTrait
{
    /**
     * Création d'une instance basée sur l'unité de gestion de stock du produit.
     *
     * @param string $sku
     *
     * @return static|null
     */
    public static function createFromSku(string $sku): ?QueryPost;

    /**
     * Récupération des attributs.
     *
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Récupération des produits de la composition.
     *
     * @return static[]|array
     */
    public function getCompositionProducts(): array;

    /**
     * Récupération des produits du groupe.
     *
     * @return static[]|array
     */
    public function getGroupedProducts(): array;

    /**
     * Récupération de la liste des types de produit.
     *
     * @return ProductObjectType
     */
    public function getProductObjectType(): ProductObjectType;

    /**
     * Récupération du la liste des étiquettes associées.
     *
     * @return WP_Term[]|array
     */
    public function getProductTags(): array;

    /**
     * Récupération du type de produit.
     *
     * @return string
     */
    public function getProductType(): string;

    /**
     * Récupération de la liste des types de produit.
     *
     * @return array
     */
    public function getProductTypes(): array;

    /**
     * Récupération de l'instance d'une option d'achat.
     *
     * @param string $name Identifiant de qualification de l'option d'achat.
     *
     * @return ProductPurchasingOption
     */
    public function getPurchasingOption(string $name): ?ProductPurchasingOption;

    /**
     * Récupération de la liste des instance d'options d'achat.
     *
     * @return ProductPurchasingOption[]|array
     */
    public function getPurchasingOptions(): array;

    /**
     * Récupération du prix de vente.
     *
     * @return float
     */
    public function getRegularPrice(): float;

    /**
     * Récupération de l'Unité de Gestion de Stock (SKU).
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Récupération des produits de montée en gamme.
     *
     * @return static[]|array
     */
    public function getUpsellProducts(): array;

    /**
     * Récupération du poids.
     *
     * @return float
     */
    public function getWeight(): float;

    /**
     * Vérifie si un produit est téléchargeable.
     *
     * @return boolean
     */
    public function isDownloadable(): bool;

    /**
     * Vérifie si un produit est mis en avant.
     *
     * @return boolean
     */
    public function isFeatured(): bool;

    /**
     * Vérifie si un produit est en stock.
     *
     * @return boolean
     */
    public function isInStock(): bool;

    /**
     * Vérifie si le type de produit correspond au type fourni.
     *
     * @param string $type Type de produit à vérifier.
     *
     * @return boolean
     */
    public function isProductType(string $type): bool;

    /**
     * Vérifie si un produit est en droit d'être commandé.
     *
     * @return boolean
     */
    public function isPurchasable(): bool;

    /**
     * Vérifie si un produit est dématérialisé (virtuel).
     *
     * @return boolean
     */
    public function isVirtual(): bool;

    /**
     * Récupération du prix de vente affiché.
     *
     * @return string
     */
    public function salePriceDisplay(): string;
}