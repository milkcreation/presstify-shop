<?php

/**
 * @name CustomTypes
 * @desc Gestion des types de posts et taxonomies relatifs à la boutique (hors gamme de produits).
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\CustomTypes;

use tiFy\Field\ToggleSwitch\ToggleSwitch;
use tiFy\PostType\PostType;
use tiFy\Taxonomy\Taxonomy;
use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\CustomTypesInterface;

class CustomTypes extends AbstractShopSingleton implements CustomTypesInterface
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                /** @var Taxonomy $taxonomyController */
                $taxonomyController = app(Taxonomy::class);

                // Récupération de la liste des identifiant de qualification des gamme de produits déclarés.
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
                        'object_type'       => $product_object_types,
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
                        'object_type'       => array_merge($product_object_types, ['product_variation']),
                    ]
                );

                // Catégorie de produit
                $taxonomyController->register(
                    'product_cat',
                    [
                        'hierarchical' => true,
                        'singular'     => __('categorie', 'tify'),
                        'plural'       => __('categories', 'tify'),
                        'show_ui'      => true,
                    ]
                );

                // Etiquette de produit
                $taxonomyController->register(
                    'product_tag',
                    [
                        'hierarchical' => false,
                        'singular'     => __('étiquette', 'tify'),
                        'plural'       => __('étiquettes', 'tify'),
                        'show_ui'      => true,
                    ]
                );

                // Classes de livraison
                // @todo

                /** @var PostType $postTypeController */
                $postTypeController = app(PostType::class);

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
        );
    }
}