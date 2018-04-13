<?php

namespace tiFy\Plugins\Shop\Products;

use tiFy\Core\Query\Controller\PostItemInterface;

interface ProductItemInterface extends PostItemInterface
{
    /**
     * Récupération de la liste des types de produit.
     *
     * @return ObjectTypes\Categorized|ObjectTypes\Uncategorized
     */
    public function getProductObjectType();

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
     * Récupération de l'Unité de Gestion de Stock (SKU).
     *
     * @return string
     */
    public function getSku();

    /**
     * Récupération du prix de vente.
     *
     * @return float
     */
    public function getRegularPrice();

    /**
     * Récupération du prix de vente affiché.
     *
     * @return string
     */
    public function salePriceDisplay();

    /**
     * Récupération du poids.
     *
     * @return float
     */
    public function getWeight();

    /**
     * Récupération des attributs.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Récupération des produits du groupe.
     *
     * @return string[]
     */
    public function getGroupedProducts();

    /**
     * Récupération des produits de montée en gamme.
     *
     * @return string[]
     */
    public function getUpsellProducts();

    /**
     * Récupération du la liste des étiquettes associées.
     *
     * @return array|\WP_Term[]
     */
    public function getProductTags();

    /**
     * Vérifie si un produit est téléchargeable.
     *
     * @return bool
     */
    public function isDownloadable();

    /**
     * Vérifie si un produit est dématérialisé (virtuel).
     *
     * @return bool
     */
    public function isVirtual();

    /**
     * Vérifie si un produit est mis en avant.
     *
     * @return bool
     */
    public function isFeatured();

    /**
     * Vérifie si un produit est en droit d'être commandé.
     *
     * @return bool
     */
    public function isPurchasable();

    /**
     * Vérifie si un produit est en stock.
     *
     * @return bool
     */
    public function isInStock();

    /**
     * Sauvegarde des données d'un produit.
     *
     * @return void
     */
    public function save();
}