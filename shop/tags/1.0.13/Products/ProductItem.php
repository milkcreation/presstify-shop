<?php

/**
 * @name ProductItem
 * @desc Controleur de récupération de données d'un produit
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Products
 * @version 1.1
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Products;

use Illuminate\Support\Arr;
use tiFy\Core\Query\Controller\AbstractPostItem;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class ProductItem extends AbstractPostItem implements ProductItemInterface, ProvideTraitsInterface
{
    use ProvideTraits;

    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel de l'Object Type.
     * @var ObjectTypes\Categorized|ObjectTypes\Uncategorized
     */
    private $productObjectType;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique.
     * @param \WP_Post $wp_post
     *
     * @return void
     */
    public function __construct(Shop $shop, \WP_Post $wp_post)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        parent::__construct($wp_post);
    }

    /**
     * Récupération de la liste des types de produit.
     *
     * @return ObjectTypes\Categorized|ObjectTypes\Uncategorized
     */
    public function getProductObjectType()
    {
        if (!is_null($this->productObjectType)) :
            return $this->productObjectType;
        endif;

        return $this->productObjectType = $this->products()->getObjectType($this->getType());
    }

    /**
     * Récupération de la liste des types de produit.
     *
     * @return array
     */
    public function getProductTypes()
    {
        return $this->getProductObjectType()->getProductTypes();
    }

    /**
     * Récupération du type de produit.
     *
     * @return string
     */
    public function getProductType()
    {
        if (!$terms = get_the_terms($this->getId(), 'product_type')) :
            return 'simple';
        elseif (is_wp_error($terms)) :
            return 'simple';
        endif;

        $term = reset($terms);
        if (!in_array($term->name, $this->getProductTypes())) :
            return 'simple';
        endif;

        return $term->name;
    }

    /**
     * Récupération de l'Unité de Gestion de Stock (SKU).
     *
     * @return string
     */
    public function getSku()
    {
        return get_post_meta($this->getId(), '_sku', true);
    }

    /**
     * Récupération du prix de vente.
     *
     * @return float
     */
    public function getRegularPrice()
    {
        return get_post_meta($this->getId(), '_regular_price', true);
    }

    /**
     * Récupération du prix de vente affiché.
     *
     * @return string
     */
    public function salePriceDisplay()
    {
        return $this->functions()->price()->html($this->getRegularPrice());
    }

    /**
     * Récupération du poids.
     *
     * @return float
     */
    public function getWeight()
    {
        return 0;
    }

    /**
     * Récupération des attributs.
     *
     * @return array
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * Récupération des options d'achat
     *
     * @return array
     */
    public function getPurchasingOptions()
    {
        return $this->getMeta('_purchasing_options', true);
    }

    /**
     * Récupération des attribut option d'achat.
     *
     * @param string $key Clé d'index de l'option ou syntaxe à point pour récupérer la valeur d'un attribut.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getPurchasingOption($key, $default = null)
    {
        return Arr::get($this->getPurchasingOptions(), $key, $default);
    }

    /**
     * Récupération des produits du groupe.
     *
     * @return string[]
     */
    public function getGroupedProducts()
    {
        return \get_post_meta($this->getId(), '_grouped_products', true) ? : [];
    }

    /**
     * Récupération des produits de montée en gamme.
     *
     * @return string[]
     */
    public function getUpsellProducts()
    {
        return \get_post_meta($this->getId(), '_upsell_ids', true) ? : [];
    }

    /**
     * Récupération du la liste des étiquettes associées.
     *
     * @return array|\WP_Term
     */
    public function getProductTags()
    {
        return \wp_get_post_terms($this->getId(), 'product_tag');
    }

    /**
     * Vérifie si un produit est téléchargeable.
     *
     * @return bool
     */
    public function isDownloadable()
    {
        return false;
    }

    /**
     * Vérifie si un produit est dématérialisé (virtuel).
     *
     * @return bool
     */
    public function isVirtual()
    {
        return false;
    }

    /**
     * Vérifie si un produit est mis en avant.
     *
     * @return bool
     */
    public function isFeatured()
    {
        if (!$terms = \wp_get_post_terms($this->getId(), 'product_visibility', ['fields' => 'names'])) :
            return false;
        elseif (is_wp_error($terms)) :
            return false;
        endif;

        return in_array('featured', $terms);
    }

    /**
     * Vérifie si un produit est en droit d'être commandé.
     *
     * @return bool
     */
    public function isPurchasable()
    {
        return ($this->getStatus() === 'publish');
    }

    /**
     * Vérifie si un produit est en stock.
     *
     * @return bool
     */
    public function isInStock()
    {
        return true;
    }

    /**
     * Sauvegarde des données d'un produit.
     *
     * @return void
     */
    public function save()
    {
        // -----------------------------------------------------------
        // TYPE DE PRODUIT
        $product_type = $this->appRequestGet('product-type', 'simple', 'POST');
        \wp_set_post_terms($this->getId(), $product_type, 'product_type');

        // -----------------------------------------------------------
        // VISIBILITE PRODUIT
        $visibility = [];

        // Mise en avant
        $featured = $this->appRequestGet('_featured', 'off', 'POST');
        if ($featured === 'on') :
            array_push($visibility, 'featured');
        endif;

        \wp_set_post_terms($this->getId(), $visibility, 'product_visibility');
    }
}