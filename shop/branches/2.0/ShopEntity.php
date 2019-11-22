<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop;

use tiFy\Contracts\PostType\PostTypeStatus;
use tiFy\Plugins\Shop\Contracts\{ShopEntity as ShopEntityContract, Shop};
use tiFy\Support\Proxy\{PostType};

class ShopEntity implements ShopEntityContract
{
    use ShopAwareTrait;

    /**
     * Liste des instances de statuts de commandes.
     * @var PostTypeStatus[]|array
     */
    protected $orderStatuses = [];

    /**
     * Indicateur de déclaration des entités de la boutique.
     * @var bool
     */
    protected $registered = false;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);

        $this->boot();

        add_action('init', function () {
            $this->register();
        });
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * Déclaration des entités de la boutique.
     *
     * @return $this
     */
    protected function register(): ShopEntityContract
    {
        if (!$this->registered) {
            $objectTypes = $this->shop()->products()->getObjectTypes();

            // Type de produit
            taxonomy()->register('product_type', [
                'hierarchical'      => false,
                'show_ui'           => false,
                'show_in_nav_menus' => false,
                'show_admin_column' => false,
                'query_var'         => is_admin(),
                'rewrite'           => false,
                'public'            => false,
                'object_type'       => $objectTypes,
            ]);

            // Visibilité d'un produit
            taxonomy()->register('product_visibility', [
                'hierarchical'      => false,
                'show_ui'           => false,
                'show_in_nav_menus' => false,
                'show_admin_column' => false,
                'query_var'         => is_admin(),
                'rewrite'           => false,
                'public'            => false,
                'object_type'       => array_merge($objectTypes, ['product_variation']),
            ]);

            // Catégorie de produit
            taxonomy()->register('product_cat', [
                'hierarchical' => true,
                'singular'     => __('categorie', 'tify'),
                'plural'       => __('categories', 'tify'),
                'show_ui'      => true,
            ]);

            // Etiquette de produit
            taxonomy()->register('product_tag', [
                'hierarchical' => false,
                'singular'     => __('étiquette', 'tify'),
                'plural'       => __('étiquettes', 'tify'),
                'show_ui'      => true,
            ]);

            // Classes de livraison
            // @todo

            // Produits
            // @todo

            // Variation de produits
            PostType::register('product_variation', [
                'plural'          => __('variations', 'tify'),
                'singular'        => __('variation', 'tify'),
                'gender'          => true,
                'public'          => false,
                'hierarchical'    => false,
                'supports'        => false,
                'capability_type' => 'product',
                'rewrite'         => false,
            ]);

            // Commandes
            PostType::register('shop_order', [
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
            ]);

            $this->orderStatuses['pending'] = PostType::status('order-pending', [
                'label'                     => _x('En attente de paiement', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'En attente de paiement <span class="count">(%s)</span>',
                    'En attente de paiement <span class="count">(%s)</span>',
                    'tify'
                ),
            ]);

            $this->orderStatuses['processing'] = PostType::status('order-processing', [
                'label'                     => _x('En préparation', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'En préparation <span class="count">(%s)</span>',
                    'En préparation <span class="count">(%s)</span>',
                    'tify'
                ),
            ]);

            $this->orderStatuses['on-hold'] = PostType::status('order-on-hold', [
                'label'                     => _x('En attente', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'En attente <span class="count">(%s)</span>',
                    'En attente <span class="count">(%s)</span>',
                    'tify'
                ),
            ]);

            $this->orderStatuses['completed'] = PostType::status('order-completed', [
                'label'                     => _x('Terminée', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'Terminée <span class="count">(%s)</span>',
                    'Terminée <span class="count">(%s)</span>',
                    'tify'
                ),
            ]);

            $this->orderStatuses['cancelled'] = PostType::status('order-cancelled', [
                'label'                     => _x('Annulée', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'Annulée <span class="count">(%s)</span>',
                    'Annulée <span class="count">(%s)</span>',
                    'tify'
                ),
            ]);

            $this->orderStatuses['refunded'] = PostType::status('order-refunded', [
                'label'                     => _x('Remboursée', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'Remboursée <span class="count">(%s)</span>',
                    'Remboursée <span class="count">(%s)</span>',
                    'tify'
                ),
            ]);

            $this->orderStatuses['failed'] = PostType::status('order-failed', [
                'label'                     => _x('Echouée', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'Echouée <span class="count">(%s)</span>',
                    'Echouée <span class="count">(%s)</span>',
                    'tify'
                ),
            ]);

            // Remboursements
            PostType::register('shop_order_refund', [
                'plural'          => __('remboursements', 'tify'),
                'singular'        => __('remboursement', 'tify'),
                'capability_type' => 'shop_order',
                'public'          => false,
                'hierarchical'    => false,
                'supports'        => false,
                'rewrite'         => false,
            ]);

            // Coupons

            // Webhook

            $this->registered = true;

            /* @todo Création des tables de base de données
            db()->register(
                'shop.order.items',
                [
                    'install'    => true,
                    'name'       => 'tify_shop_order_items',
                    'primary'    => 'order_item_id',
                    'col_prefix' => 'order_item_',
                    'meta'       => [
                        'meta_type' => 'tify_shop_order_item',
                        'join_col'  => 'order_item_id'
                    ],
                    'columns'    => [
                        'id'       => [
                            'type'           => 'BIGINT',
                            'size'           => 20,
                            'unsigned'       => true,
                            'auto_increment' => true
                        ],
                        'name'     => [
                            'type' => 'TEXT',
                        ],
                        'type'     => [
                            'type'    => 'VARCHAR',
                            'size'    => 200,
                            'default' => ''
                        ],
                        'order_id' => [
                            'type'     => 'BIGINT',
                            'size'     => 20,
                            'unsigned' => true,
                            'prefix'   => false
                        ]
                    ],
                    'keys'       => ['order_id' => ['cols' => 'order_id', 'type' => 'INDEX']],
                ]
            ); */
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrderStatuses(): array
    {
        return $this->orderStatuses;
    }
}