<?php

/**
 * @name CustomTypes
 * @desc Gestion des types de post et taxonomie relatifs à la boutique (hors gamme de produits)
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\CustomTypes
 * @version 1.1
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\CustomTypes;

use tiFy\App\Traits\App as TraitsApp;
use tiFy\Core\CustomType\CustomType;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;

class CustomTypes implements CustomTypesInterface, ProvideTraitsInterface
{
    use TraitsApp, ProvideTraits;

    /**
     * Instance de la classe
     * @var CustomTypes
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    protected function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Déclaration des événements de déclenchement
        $this->appAddAction('tify_custom_taxonomy_register');
        $this->appAddAction('tify_custom_post_type_register');
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Instanciation de la classe
     *
     * @param Shop $shop
     *
     * @return CustomTypes
     */
    public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * Déclaration des types de taxonomies personnalisées
     *
     * @return void
     */
    final public function tify_custom_taxonomy_register()
    {
        // Récupération de la liste des identifiant de qualification des gamme de produits déclarés
        $product_object_types = $this->products()->getObjectTypes();

        // Type de produit
        CustomType::registerTaxonomy(
            'product_type',
            [
                'hierarchical'      => false,
                'show_ui'           => false,
                'show_in_nav_menus' => false,
                'show_admin_column' => false,
                'query_var'         => is_admin(),
                'rewrite'           => false,
                'public'            => false,

                'object_type' => $product_object_types,
            ]
        );

        // Visibilité d'un produit
        CustomType::registerTaxonomy(
            'product_visibility',
            [
                'hierarchical'      => false,
                'show_ui'           => false,
                'show_in_nav_menus' => false,
                'show_admin_column' => false,
                'query_var'         => is_admin(),
                'rewrite'           => false,
                'public'            => false,

                'object_type' => array_merge($product_object_types, ['product_variation']),
            ]
        );

        // Catégorie de produit
        // @todo

        // Etiquette de produit
        // @todo

        // Classes de livraison
        // @todo
    }

    /**
     * Déclaration des types de posts personnalisés des gammes de produits
     *
     * @return void
     */
    final public function tify_custom_post_type_register()
    {
        // Produits
        // @todo

        // Variation de produits
        CustomType::registerPostType(
            'product_variation',
            [
                'plural'          => __('variations', 'tify'),
                'singular'        => __('variation', 'tify'),
                'gender'          => true,
                'public'          => false,
                'hierarchical'    => false,
                'supports'        => false,
                'capability_type' => 'product',
                'rewrite'         => false,
            ]
        );

        // Commandes
        CustomType::registerPostType(
            'shop_order',
            [
                'plural'              => __('commandes', 'tify'),
                'singular'            => __('commande', 'tify'),
                'gender'              => true,
                'public'              => false,
                'show_ui'             => true,
                'capability_type'     => 'shop_order',
                'map_meta_cap'        => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'show_in_menu'        => current_user_can('manage_tify_shop') ? 'tify_shop' : true,
                'hierarchical'        => false,
                'show_in_nav_menus'   => false,
                'rewrite'             => false,
                'query_var'           => false,
                'supports'            => ['title'],
                'has_archive'         => false,
            ]
        );

        // Remboursements
        CustomType::registerPostType(
            'shop_order_refund',
            [
                'plural'          => __('remboursements', 'tify'),
                'singular'        => __('remboursement', 'tify'),
                'capability_type' => 'shop_order',
                'public'          => false,
                'hierarchical'    => false,
                'supports'        => false,
                'rewrite'         => false,
            ]
        );

        // Coupons
        // @todo

        // Webhook
        // @todo
    }
}