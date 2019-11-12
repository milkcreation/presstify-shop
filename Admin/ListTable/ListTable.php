<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Admin\ListTable;

use tiFy\Contracts\Column\Column;
use tiFy\Plugins\Shop\Contracts\{ProductObjectType, Shop};
use tiFy\Plugins\Shop\Products\ObjectType\{Categorized, Uncategorized};
use tiFy\Plugins\Shop\ShopAwareTrait;
use WP_Screen;

class ListTable
{
    use ShopAwareTrait;

    /**
     * Nom de qualification du type de post associé.
     * @var string
     */
    private $objectName = '';

    /**
     * Instance du type de produit.
     * @var Categorized|Uncategorized
     */
    private $objectType;

    /**
     * CONSTRUCTEUR.
     *
     * @param ProductObjectType $object_type Instance du type de produit.
     * @param Shop $shop Attributs de configuration.
     *
     * @return void
     */
    public function __construct(ProductObjectType $object_type, Shop $shop)
    {
        $this->setShop($shop);

        $this->objectType = $object_type;
        $this->objectName = $this->objectType->getName();

        /** @var Column $column */
        $column = app('column');
        $column
            ->add(
                "{$this->objectName}@post_type",
                'thumb',
                [
                    'title'    => '<span class="dashicons dashicons-format-image"></span>',
                    'position' => 1,
                    'content'  => [$this, 'columnThumbnail'],
                ]
            )
            ->add(
                "{$this->objectName}@post_type",
                'product_type',
                [
                    'title'    => __('Type', 'tify'),
                    'position' => 2.1,
                    'content'  => [$this, 'columnProductType'],
                ]
            )
            ->add(
                "{$this->objectName}@post_type",
                'sku',
                [
                    'title'    => __('UGS', 'tify'),
                    'position' => 2.2,
                    'content'  => [$this, 'columnSku'],
                ]
            )
            ->add(
                "{$this->objectName}@post_type",
                'price',
                [
                    'title'    => __('Prix', 'tify'),
                    'position' => 2.3,
                    'content'  => [$this, 'columnPrice'],
                ]
            )
            ->add(
                "{$this->objectName}@post_type",
                'featured',
                [
                    'title'    => "<span class=\"dashicons dashicons-star-half\"></span>",
                    'position' => 10,
                    'content'  => [$this, 'columnFeatured'],
                ]
            );

        add_action('current_screen', function (WP_Screen $wp_screen) {
            if ($wp_screen->id === "edit-{$this->objectName}") {
                add_action('admin_enqueue_scripts', function () {
                    partial('holder')->enqueue();

                    wp_enqueue_style(
                        'ShopAdminProductList',
                        $this->shop()->resourcesUrl() . '/assets/css/admin-list.css',
                        [],
                        181103
                    );
                }
                );
            }
        }
        );
    }

    /**
     * Contenu des éléments de la colonne "Mise en avant".
     *
     * @param string $column_name Identifiant de qualification de la colonne.
     * @param int $post_id Identifiant du contenu.
     *
     * @return string
     */
    public function columnFeatured($column_name, $post_id)
    {
        $product = $this->shop->products()->get($post_id);

        return $product->isFeatured()
            ? "<span class=\"dashicons dashicons-star-filled\"></span>"
            : "<span class=\"dashicons dashicons-star-empty\"></span>";
    }

    /**
     * Contenu des éléments de la colonne "Prix".
     *
     * @param string $column_name Identifiant de qualification de la colonne.
     * @param int $post_id Identifiant du contenu.
     *
     * @return string
     */
    public function columnPrice($column_name, $post_id)
    {
        $product = $this->shop()->products()->get($post_id);

        if ($product->isProductType('composing')) :
            return '<em>' . __('Non vendu séparément', 'tify') . '</em>';
        else :
            return ($price = $product->getRegularPrice())
                ? $this->shop()->functions()->price()->html($price)
                : '--';
        endif;
    }

    /**
     * Contenu des éléments de la colonne "Type de produit".
     *
     * @param string $column_name Identifiant de qualification de la colonne.
     * @param int $post_id Identifiant du contenu.
     *
     * @return string
     */
    public function columnProductType($column_name, $post_id)
    {
        $product = $this->shop()->products()->get($post_id);

        return (string)partial('tag', [
            'tag'     => 'a',
            'attrs'   => [
                'href'  => '#',
                'title' => $this->shop()->products()->getProductTypeDisplayName($product->getProductType()),
            ],
            'content' => $this->shop()->products()->getProductTypeIcon($product->getProductType()),
        ]);
    }

    /**
     * Contenu des éléments de la colonne "Unité de Gestion de Stock" (sku).
     *
     * @param string $column_name Identifiant de qualification de la colonne.
     * @param int $post_id Identifiant du contenu.
     *
     * @return string
     */
    public function columnSku($column_name, $post_id)
    {
        return get_post_meta($post_id, '_sku', true) ?: '';
    }

    /**
     * Contenu des éléments de la colonne "Unité de Gestion de Stock" (sku).
     *
     * @param string $column_name Identifiant de qualification de la colonne.
     * @param int $post_id Identifiant du contenu.
     *
     * @return string
     */
    public function columnThumbnail($column_name, $post_id)
    {
        $product = $this->shop()->products()->get($post_id);

        if ($thumb = $product->getThumbnail([80, 80])) {
        } else {
            $thumb = partial('holder', [
                'width'  => 80,
                'height' => 80,
            ]);
        }

        return $thumb;
    }
}