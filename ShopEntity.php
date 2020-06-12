<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use tiFy\Contracts\PostType\PostTypeStatus;
use tiFy\Plugins\Shop\Contracts\ShopEntity as ShopEntityContract;
use tiFy\Support\Proxy\{Database, PostType, Schema, Taxonomy};

class ShopEntity implements ShopEntityContract
{
    use ShopAwareTrait;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    protected $booted = false;

    /**
     * Liste des instances de statuts de commandes.
     * @var PostTypeStatus[]|array
     */
    protected $orderStatuses = [];

    /**
     * @inheritDoc
     */
    public function boot(): ShopEntityContract
    {
        if (!$this->booted) {
            /** Type de produit */
            Taxonomy::register('product_type', [
                'hierarchical'      => false,
                'show_ui'           => false,
                'show_in_nav_menus' => false,
                'show_admin_column' => false,
                'query_var'         => is_admin(),
                'rewrite'           => false,
                'public'            => false,
                'object_type'       => $this->shop()->products()->getObjectTypeNames(),
            ]);
            /**/

            /** Visibilité d'un produit */
            Taxonomy::register('product_visibility', [
                'hierarchical'      => false,
                'show_ui'           => false,
                'show_in_nav_menus' => false,
                'show_admin_column' => false,
                'query_var'         => is_admin(),
                'rewrite'           => false,
                'public'            => false,
                'object_type'       => array_merge(
                    $this->shop()->products()->getObjectTypeNames(), ['product_variation']
                ),
            ]);
            /**/

            /** Catégorie de produit */
            Taxonomy::register('product_cat', [
                'hierarchical' => true,
                'singular'     => __('categorie', 'tify'),
                'plural'       => __('categories', 'tify'),
                'show_ui'      => true,
            ]);
            /**/

            /** Etiquette de produit */
            Taxonomy::register('product_tag', [
                'hierarchical' => false,
                'singular'     => __('étiquette', 'tify'),
                'plural'       => __('étiquettes', 'tify'),
                'show_ui'      => true,
            ]);
            /**/

            // Classes de livraison.
            // @todo EVOLUTION : Mettre en oeuvre

            // Produits.
            // @todo EVOLUTION : Mettre en oeuvre

            /** Variation de produits */
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
            /**/

            /** Commandes */
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

            /** Remboursements */
            PostType::register('shop_order_refund', [
                'plural'          => __('remboursements', 'tify'),
                'singular'        => __('remboursement', 'tify'),
                'capability_type' => 'shop_order',
                'public'          => false,
                'hierarchical'    => false,
                'supports'        => false,
                'rewrite'         => false,
            ]);

            /** Statuts de commandes */
            $this
                /* Commande - En cours de réglement */
                ->setOrderStatus('pending', PostType::status('order-pending', [
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
                ]))
                /**/
                /* Commande - En préparation */
                ->setOrderStatus('processing', PostType::status('order-processing', [
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
                ]))
                /**/
                /* Commande - En attente */
                ->setOrderStatus('on-hold', PostType::status('order-on-hold', [
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
                ]))
                /**/
                /* Commande - Terminée */
                ->setOrderStatus('completed', PostType::status('order-completed', [
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
                ]))
                /**/
                /* Commande - Annulée */
                ->setOrderStatus('cancelled', PostType::status('order-cancelled', [
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
                ]))
                /**/
                /* Commande - Remboursée */
                ->setOrderStatus('refunded', PostType::status('order-refunded', [
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
                ]))
                /**/
                /* Commande - Echouée */
                ->setOrderStatus('failed', PostType::status('order-failed', [
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
                ]));
            /**/

            /** Coupons */
            // @todo EVOLUTION : Mettre en oeuvre

            /** Base de données */
            if (!Schema::hasTable('tify_shop_order_items')) {
                Schema::create('tify_shop_order_items', function (Blueprint $table) {
                    $table->bigIncrements('order_item_id');
                    $table->text('order_item_name');
                    $table->string('order_item_type', 200);
                    $table->bigInteger('order_id')->unsigned();
                    $table->index('order_id', 'order_id');
                });
            }

            if (!Schema::hasTable('tify_shop_order_itemmeta')) {
                Schema::create('tify_shop_order_itemmeta', function (Blueprint $table) {
                    $table->bigIncrements('meta_id');
                    $table->bigInteger('order_item_id')->default(0);
                    $table->string('meta_key', 255)->nullable();
                    $table->longText('meta_value')->nullable();
                    $table->index('order_item_id', 'order_item_id');
                    $table->index('meta_key', 'meta_key');
                });
            }
            /**/

            $this->booted = true;
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

    /**
     * @inheritDoc
     */
    public function orderItemsTable(): Builder
    {
        return Database::table('tify_shop_order_items');
    }

    /**
     * @inheritDoc
     */
    public function orderItemMetaTable(): Builder
    {
        return Database::table('tify_shop_order_itemmeta');
    }

    /**
     * @inheritDoc
     */
    public function setOrderStatus(string $alias, PostTypeStatus $status): ShopEntityContract
    {
        $this->orderStatuses[$alias] = $status;

        return $this;
    }
}