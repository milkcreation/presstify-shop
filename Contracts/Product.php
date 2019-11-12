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
    public function getAttributes();

    /**
     * Récupération des produits de la composition.
     *
     * @return ProductsCollection|array
     */
    public function getCompositionProducts();

    /**
     * Récupération des produits du groupe.
     *
     * @return ProductsCollection|array
     */
    public function getGroupedProducts();

    /**
     * Récupération de la liste des types de produit.
     *
     * @return ProductObjectType
     */
    public function getProductObjectType();

    /**
     * Récupération du la liste des étiquettes associées.
     *
     * @return WP_Term|array
     */
    public function getProductTags();

    /**
     * Récupération de la liste des types de produit.
     *
     * @return array
     */
    public function getProductTypes();

    /**
     * Récupération du type de produit.
     *
     * @return string
     */
    public function getProductType();

    /**
     * Récupération des attribut option d'achat.
     *
     * @param string $name Identifiant de qualification de l'option d'achat.
     *
     * @return ProductPurchasingOption
     */
    public function getPurchasingOption($name);

    /**
     * Récupération de la liste des options d'achat.
     *
     * @return array|ProductPurchasingOption[]
     */
    public function getPurchasingOptions();

    /**
     * Récupération du prix de vente.
     *
     * @return float
     */
    public function getRegularPrice();

    /**
     * Récupération de l'Unité de Gestion de Stock (SKU).
     *
     * @return string
     */
    public function getSku();

    /**
     * Récupération des produits de montée en gamme.
     *
     * @return string[]
     */
    public function getUpsellProducts();

    /**
     * Récupération du poids.
     *
     * @return float
     */
    public function getWeight();

    /**
     * Vérifie si un produit est téléchargeable.
     *
     * @return bool
     */
    public function isDownloadable();

    /**
     * Vérifie si un produit est mis en avant.
     *
     * @return bool
     */
    public function isFeatured();

    /**
     * Vérifie si un produit est en stock.
     *
     * @return bool
     */
    public function isInStock();

    /**
     * Vérifie si le type de produit correspond au type fourni.
     *
     * @param string $type Type de produit à vérifier
     *
     * @return boolean
     */
    public function isProductType($type);

    /**
     * Vérifie si un produit est en droit d'être commandé.
     *
     * @return bool
     */
    public function isPurchasable();

    /**
     * Vérifie si un produit est dématérialisé (virtuel).
     *
     * @return bool
     */
    public function isVirtual();

    /**
     * Récupération du prix de vente affiché.
     *
     * @return string
     */
    public function salePriceDisplay();
}