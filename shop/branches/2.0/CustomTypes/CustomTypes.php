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

use tiFy\Apps\AppController;
use tiFy\PostType\PostType;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Taxonomy\Taxonomy;

class CustomTypes extends AppController implements CustomTypesInterface, ProvideTraitsInterface
{
    use ProvideTraits;

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
        $this->appAddAction('tify_taxonomy_register');
        $this->appAddAction('tify_post_type_register');
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
     * @param Taxonomy $taxonomyController Classe de rappel du controleur de taxonomie.
     *
     * @return void
     */
    final public function tify_taxonomy_register($taxonomyController)
    {
        // Récupération de la liste des identifiant de qualification des gamme de produits déclarés
        $product_object_types = $this->products()->getObjectTypes();

        // Type de produit
        $taxonomyController->register(
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
        $taxonomyController->register(
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
        $taxonomyController->register(
            'product_cat',
            [
                'hierarchical'          => true,
                'singular'              => __('categorie', 'tify'),
                'plural'                => __('categories', 'tify'),
                'show_ui'               => true
            ]
        );

        // Etiquette de produit
        $taxonomyController->register(
            'product_tag',
            [
                'hierarchical'          => false,
                'singular'              => __('étiquette', 'tify'),
                'plural'                => __('étiquettes', 'tify'),
                'show_ui'               => true
            ]
        );

        // Classes de livraison
        // @todo
    }

    /**
     * Déclaration des types de posts personnalisés des gammes de produits
     *
     * @param PostType $postTypeController Classe de rappel du controleur de type de post.
     *
     * @return void
     */
    final public function tify_post_type_register($postTypeController)
    {
        // Produits
        // @todo

        // Variation de produits
        $postTypeController->register(
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
        $postTypeController->register(
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
                //'show_in_menu'        => current_user_can('manage_tify_shop') ? 'tify_shop' : true,
                'hierarchical'        => false,
                'show_in_nav_menus'   => false,
                'rewrite'             => false,
                'query_var'           => false,
                'supports'            => ['title'],
                'has_archive'         => false,
            ]
        );

        // Remboursements
        $postTypeController->register(
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