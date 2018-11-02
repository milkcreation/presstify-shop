<?php

/**
 * Exemple de configuration.
 * @todo
 */

return [
    //@var $providers Fournisseurs de services
    'providers' => [
        'addresses.billing'              => Billing::class,
        'addresses.form_handler'         => FormHandler::class,
        'addresses.shipping'             => Shipping::class,
        'cart.controller'                => Cart::class,
        'cart.line'                      => Line::class,
        'cart.session_items'             => SessionItems::class,
        'checkout.controller'            => Checkout::class,
        'gateways.cheque'                => Cheque::class,
        'gateways.payzen'                => Payzen::class,
        'functions.page'                 => Page::class,
        'functions.url'                  => Url::class,
        'orders.order'                   => Order::class,
        'orders.order_item_type_product' => OrderItemTypeProduct::class,
        'orders.list'                    => OrderList::class,
        'products.controller'            => ProductQuery::class,
        'products.list'                  => ProductList::class,
        'users.customer'                 => CustomerItem::class,
        'users.shop_manager'             => ShopManagerItem::class,
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

    // @var array $login Déclarations attributs de configuration de l'interface d'authentification à la boutique
    // @todo Surchage provider + inclusion de attributs de configuration dans users
    'signin'           => [
        'controller'   => SignInController::class,
        'form'         => [
            'class'  => 'tiFyForm-Container--login',
            'fields' => [
                'username' => [
                    'label' => false,
                    'attrs' => [
                        'class'        => 'input tiFySignIn-Input tiFySignIn-Input--text tiFySignIn-UsernameInput',
                        'placeholder'  => __('Entrez ici votre identifiant', 'theme'),
                        'size'         => 20,
                        'autocomplete' => 'username',
                    ],
                ],
                'password' => [
                    'label' => false,
                    'attrs' => [
                        'class'        => 'input tiFySignIn-Input tiFySignIn-Input--text tiFySignIn-PasswordInput',
                        'placeholder'  => __('Entrez ici votre mot de passe', 'theme'),
                        'size'         => 20,
                        'autocomplete' => 'current-password',
                    ],
                ],
                'submit'   => [
                    'content' => __('Connexion', 'theme'),
                    'attrs'   => [
                        'class' => 'input tiFySignIn-Input tiFySignIn-Input--submit tiFySignIn-SubmitInput Button Button--1',
                    ],
                ],
            ],
        ],
        'roles'        => ['customer', 'shop_manager'],
        'redirect_url' => site_url('/'),
    ],

    // @var array $products Déclaration des gammes de produits
    'products'         => [
        // @var string $post_type Identifiant de qualification du type de post de la gamme de produit
        'product' => [
            // @var $item_controller Controleur de récupération des données d'un produit
            'item_controller' => ProductItem::class,

            // @var bool|string Liste des attributs de configuration du type de post
            // @see \tiFy\CustomType\CustomType\README.md

            // @var string $singular Intitulé d'un élément de la gamme
            'singular'        => __('Produit', 'theme'),
            // @var string $plural Intitulé de plusieurs éléments de la gamme
            'plural'          => __('Produits', 'theme'),

            'publicly_queryable' => false,
            'supports'           => ['title', 'editor'],

            // @var bool|array $category Gestion de la hiérarchisation par catégorie de la gamme de produit
            'category'           => false
            /*
                [
                    // Liste des attributs de configuration de la taxonomy
                    // @see wp-content/mu-plugins/docs/core/CustomType/README.md
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
                    'title'    => __('Caracteristiques', 'theme'),
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
    // @todo Surchage provider + inclusion de attributs de configuration dans users
    'roles'            => [
        // @var string $id Identifiant de qualification d'un rôle ex. Gestionnaire boutique
        'shop_manager' => [
            // @var bool|string Liste des attributs de configuration du rôle
            // @see \tiFy\CustomType\CustomType\README.md

            // @var string $display_name Nom d'affichage
            'display_name' => __('Gestionnaire boutique', 'theme'),

            // @var bool|array $admin_ui Interface d'administration dédiée
            // @var see \tiFy\Ui\Ui\README.md
            'admin_ui'     => [
                'global' => [
                    'admin_menu' => [
                        'menu_title' => __('Gestionnaires', 'theme'),
                        'icon_url'   => 'dashicons-businessman',
                        'position'   => 71,
                    ],
                ],
                'list'   => [
                    'admin_menu' => [
                        'menu_title' => __('Tous les gestionnaires', 'theme'),
                    ],
                ],
            ],
        ],

        // @var string $id Identifiant de qualification d'un rôle ex. Gestionnaire boutique
        'customer'     => [
            // @var bool|string Liste des attributs de configuration du rôle
            // @see \tiFy\CustomType\CustomType\README.md

            // @var string $display_name Nom d'affichage
            'display_name' => __('Client', 'theme'),

            // @var bool|array $admin_ui Interface d'administration dédiée
            // @var see \tiFy\Ui\Ui\README.md
            'admin_ui'     => [
                'global' => [
                    'admin_menu' => [
                        'menu_title' => __('Clients', 'theme'),
                        'icon_url'   => 'dashicons-cart',
                        'position'   => 72,
                    ],
                ],
                'list'   => [
                    'admin_menu' => [
                        'menu_title' => __('Tous les clients', 'theme'),
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