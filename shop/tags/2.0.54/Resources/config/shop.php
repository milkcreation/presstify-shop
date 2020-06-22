<?php

/**
 * Exemple de configuration.
 */
return [
    //@var $providers Fournisseurs de services
    'providers' => [

    ],

    // @var array $addresses Déclaration des attributs de configuration des adresse
    'addresses'        => [],

    // @var array $cart Déclaration des attributs de configuration du panier
    'cart'             => [
        'notices' => [

        ],
    ],

    // @var array $gateways Déclaration des attributs de configuration du panier
    'gateways'         => [
        'cash_on_delivery' => false,
        'cheque',
        'payzen'           => [
            'site_id'  => '33149537',
            'key_test' => '4426438244783683',
            'debug'    => true,
            'ctx_mode' => 'TEST',
        ],
    ],

    // @var array $products Déclaration des gammes de produits
    'products'         => [
        // @var string $post_type Identifiant de qualification du type de post de la gamme de produit
        'product' => [
            // @var bool|string Liste des attributs de configuration du type de post

            // @var string $singular Intitulé d'un élément de la gamme
            'singular'        => __('Produit', 'tify'),
            // @var string $plural Intitulé de plusieurs éléments de la gamme
            'plural'          => __('Produits', 'tify'),

            'publicly_queryable' => false,
            'supports'           => ['title', 'editor'],

            // @var bool|array $category Gestion de la hiérarchisation par catégorie de la gamme de produit
            'category'           => false
            /*
                [
                    // Liste des attributs de configuration de la taxonomy
                    'taxonomy' => 'product_cat'
                ]
            */,

            // @var array $product_types Liste des types de produits permis(simple|grouped|external|variable)
            'product_types'      => ['simple', 'grouped', 'variable'],

            // @var array $tabs Liste des onglets de saisie
            // @see wp-content/mu-plugins/docs/core/Taboox/README.md
            'tabs'               => [
                /*'general'    => [
                    'content' => [Admin::get('Product'), 'metaboxGeneral'],
                ],
                'inventory'  => false,
                'shipping'   => false,
                'linked'     => [
                    'content' => [Admin::get('Product'), 'metaboxLinked'],
                ],
                'attributes' => false,
                'features'   => [
                    'title'    => __('Caracteristiques', 'tify'),
                    'position' => 3,
                    'content'  => [Admin::get('Product'), 'metaboxFeatures'],
                ],
                'variations' => false,
                'advanced'   => false,
                */
            ],
        ],
    ],

    // @var array $roles Déclarations des roles et habilitations de la boutique
    'roles'            => [
        // @var string $id Identifiant de qualification d'un rôle ex. Gestionnaire boutique
        'shop_manager' => [
            // @var bool|string Liste des attributs de configuration du rôle

            // @var string $display_name Nom d'affichage
            'display_name' => __('Gestionnaire boutique', 'tify'),

            // @var bool|array $admin_ui Interface d'administration dédiée
            // @var see \tiFy\Ui\Ui\README.md
            'admin_ui'     => [
                'global' => [
                    'admin_menu' => [
                        'menu_title' => __('Gestionnaires', 'tify'),
                        'icon_url'   => 'dashicons-businessman',
                        'position'   => 71,
                    ],
                ],
                'list'   => [
                    'admin_menu' => [
                        'menu_title' => __('Tous les gestionnaires', 'tify'),
                    ],
                ],
            ],
        ],

        // @var string $id Identifiant de qualification d'un rôle ex. Gestionnaire boutique
        'customer'     => [
            // @var bool|string Liste des attributs de configuration du rôle

            // @var string $display_name Nom d'affichage
            'display_name' => __('Client', 'tify'),

            // @var bool|array $admin_ui Interface d'administration dédiée
            // @var see \tiFy\Ui\Ui\README.md
            'admin_ui'     => [
                'global' => [
                    'admin_menu' => [
                        'menu_title' => __('Clients', 'tify'),
                        'icon_url'   => 'dashicons-cart',
                        'position'   => 72,
                    ],
                ],
                'list'   => [
                    'admin_menu' => [
                        'menu_title' => __('Tous les clients', 'tify'),
                    ],
                ],
            ],
        ],
    ],

    // @var array $settings Déclaration des options de la boutique
    'settings'         => [
        'decimal_separator' => ',',
    ]
];