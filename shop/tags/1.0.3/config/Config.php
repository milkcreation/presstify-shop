<?php
namespace App\Plugins\Shop;

class Config extends \tiFy\App\Config
{
    /**
     * Définition globale des attributs de configuration
     *
     * @param mixed $attrs Liste des attributs existants
     *
     * @return array|mixed
     */
    public function sets($attrs = [])
    {
       return [
            // @var array $products Déclaration des gammes de produits
            'products' => [
                // @var string $post_type Identifiant de qualification du type de post de la gamme de produit
                'product' => [
                    // @var bool|string Liste des attributs de configuration du type de post
                    // @see \tiFy\Core\CustomType\CustomType\README.md

                    // @var string $singular Intitulé d'un élément de la gamme
                    'singular' => __('Produit', 'tify'),
                    // @var string $plural Intitulé de plusieurs éléments de la gamme
                    'plural' => __('Produits', 'tify'),

                    // @var bool|array $category Gestion de la hiérarchisation par catégorie de la gamme de produit
                    'category'=> [
                        // Liste des attributs de configuration de la taxonomy
                        // @see \tiFy\Core\CustomType\CustomType\README.md
                        'taxonomy' => 'product_cat'
                    ]
                ]
            ],

            // @var array $roles Déclarations des roles et habilitations de la boutique
            'roles' => [
                // @var string $id Identifiant de qualification d'un rôle ex. Gestionnaire boutique
                'shop_manager' => [
                    // @var bool|string Liste des attributs de configuration du rôle
                    // @see \tiFy\Core\CustomType\CustomType\README.md

                    // @var string $display_name Nom d'affichage
                    'display_name' => __('Gestionnaire boutique', 'tify'),

                    // @var bool|array $admin_ui Interface d'administration dédiée
                    // @var see \tiFy\Core\Ui\Ui\README.md
                    'admin_ui' => [
                        'global' => [
                            'admin_menu' => [
                                'menu_title' => __('Gestionnaires', 'tify'),
                                'icon_url' => 'dashicons-businessman',
                                'position' => 71
                            ]
                        ],

                        'list' => [
                            'admin_menu' => [
                                'menu_title' => __('Tous les gestionnaires', 'tify'),
                            ]
                        ]
                    ]
                ],

                // @var string $id Identifiant de qualification d'un rôle ex. Gestionnaire boutique
                'customer'      => [
                    // @var bool|string Liste des attributs de configuration du rôle
                    // @see \tiFy\Core\CustomType\CustomType\README.md

                    // @var string $display_name Nom d'affichage
                    'display_name' => __('Client', 'tify'),

                    // @var bool|array $admin_ui Interface d'administration dédiée
                    // @var see \tiFy\Core\Ui\Ui\README.md
                    'admin_ui' => [
                        'global' => [
                            'admin_menu' => [
                                'menu_title' => __('Clients', 'tify'),
                                'icon_url' => 'dashicons-cart',
                                'position' => 72
                            ]
                        ],

                        'list' => [
                            'admin_menu' => [
                                'menu_title' => __('Tous les clients', 'tify'),
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}