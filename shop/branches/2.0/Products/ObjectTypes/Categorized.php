<?php

/**
 * @name Categorized
 * @desc Gestion des produits de la gamme, catégorisés
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Products\ObjectTypes
 * @version 1.1
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Products\ObjectTypes;

use tiFy\Components;
use tiFy\CustomType\CustomType;
use tiFy\Taboox\Taboox;
use tiFy\Plugins\Shop\Shop;

class Categorized extends Factory
{
    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     * @param string $post_type Identifiant de qualification du type de post
     * @param array $attrs Attributs de configuration
     *
     * @return void
     */
    public function __construct(Shop $shop, $post_type, $attrs = [])
    {
        parent::__construct($shop, $post_type, $attrs);

        // Déclaration des événements de déclenchement
        $this->appAddAction('tify_custom_taxonomy_register');
        $this->appAddAction('restrict_manage_posts', null, null, 2);
        $this->appAddAction('tify_taboox_register_box');
        $this->appAddAction('tify_taboox_register_node');
    }

    /**
     * Traitement de arguments de configuration
     *
     * @param array $attrs
     *
     * @return array
     */
    final protected function parseAttrs($attrs = [])
    {
        $attrs = parent::parseAttrs($attrs);

        $defaults_category = [
            'taxonomy'     => 'tifyshopcat-' . $this->getId(),
            'object_type'  => [$this->getId()],
            'gender'       => true,
            'label'        => _x('Catégories de produits', 'taxonomy general name', 'tify'),
            'plural'       => _x('catégories de produits', 'taxonomy plural name', 'tify'),
            'singular'     => _x('catégorie de produits', 'taxonomy singular name', 'tify'),
            'hierarchical' => true,
        ];
        $attrs['category'] = isset($attrs['category']) ? \wp_parse_args($attrs['category'],
            $defaults_category) : $defaults_category;

        if (is_string($attrs['category']['object_type'])) :
            $attrs['category']['object_type'] = (array)$attrs['category']['object_type'];
        endif;

        if (!in_array($this->getId(), $attrs['category']['object_type'])) :
            $attrs['category']['object_type'][] = $this->getId();
        endif;

        return $attrs;
    }

    /**
     * Déclaration des catégories type pour les gammes de produits multiple
     *
     * @return \tiFy\CustomType\CustomType::registerTaxonomy()
     */
    final public function tify_custom_taxonomy_register()
    {
        return CustomType::registerTaxonomy(
            $this->getCat(),
            $this->getAttr('category', [])
        );
    }

    /**
     *
     */
    public function tify_taboox_register_box()
    {
        Taboox::registerBox(
            'edit-' . $this->getCat(),
            [
                'title'       => __('Réglages de la catégorie de produits', 'tify'),
                'object_type' => 'taxonomy',
                'object_name' => $this->getCat(),
            ]
        );
    }

    /**
     *
     */
    public function tify_taboox_register_node()
    {
        Taboox::registerNode(
            'edit-' . $this->getCat(),
            [
                'id'    => $this->getCat() . '--icon',
                'title' => __('Icône représentative', 'tify'),
                'cb'    => 'tiFy\Taboox\Taxonomy\Icon\Admin\Icon',
            ]
        );
        Taboox::registerNode(
            'edit-' . $this->getCat(),
            [
                'id'    => $this->getCat() . '--order',
                'title' => __('Ordre d\'affichage', 'tify'),
                'cb'    => 'tiFy\Taboox\Taxonomy\Order\Admin\Order',
            ]
        );
    }

    /**
     * Liste de selection de filtrage de catégorie de la page liste
     *
     * @param string $post_type
     * @param string $which top|bottom
     */
    final public function restrict_manage_posts($post_type, $which)
    {
        if ($which !== 'top') :
            return;
        endif;
        if ($post_type !== $this->getId()) :
            return;
        endif;

        $selected = !empty($_REQUEST['tFyShop-CatFilter']) ? $_REQUEST['tFyShop-CatFilter'] : '';
        ?>
        <select name="tFyShop-CatFilter">
            <option value="">
                <?php echo $this->getCatAttr('singular',
                    _x('catégorie de produits', 'taxonomy singular name', 'tify')); ?>
            </option>
            <?php foreach ((array)get_terms(['taxonomy' => $this->getCat(), 'hide_empty' => true]) as $t) : ?>
                <option value="<?php echo $t->slug; ?>" <?php selected($selected, $t->slug); ?>>
                    <?php echo $t->name ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /**
     * Récupération des attributs de configuration de la catégorie de gamme de produit
     *
     * @return array
     */
    final public function getCatAttrList()
    {
        return $this->getAttr('category', []);
    }

    /**
     * Récupération d'un attribut de configuration de la catégorie de gamme de produit
     *
     * @return mixed
     */
    final public function getCatAttr($name, $default = '')
    {
        $attrs = $this->getCatAttrList();

        if (!isset($attrs[$name])) :
            return $default;
        endif;

        return $attrs[$name];
    }

    /**
     * Récupération de la taxonomie de la catégorie de gamme de produit
     *
     * @return string
     */
    final public function getCat()
    {
        return $this->getCatAttr('taxonomy', '');
    }
}