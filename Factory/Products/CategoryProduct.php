<?php
namespace tiFy\Plugins\Shop\Factory\Products;

use tiFy\Components;
use tiFy\Components\CustomColumns\CustomColumns;
use tiFy\Core\CustomType\CustomType;
use tiFy\Core\Taboox\Taboox;

class CategoryProduct extends \tiFy\Plugins\Shop\Factory\Products\Factory
{
    /**
     * CONSTRUCTEUR
     */
    public function __construct( $id, $attrs = array() )
    {
        parent::__construct($id, $attrs);

        // Déclaration des événements de déclenchement
        $this->tFyAppActionAdd('tify_custom_taxonomy_register');
        $this->tFyAppActionAdd('restrict_manage_posts', null, null, 2);
        $this->tFyAppActionAdd('tify_components_register');
        $this->tFyAppActionAdd('tify_custom_columns_register');
        $this->tFyAppActionAdd('tify_taboox_register_box');
        $this->tFyAppActionAdd('tify_taboox_register_node');
    }
    
    /**
     * DECLENCHEURS
     */
    /**
     * Déclaration des catégories type pour les gammes de produits multiple
     *
     * @return \tiFy\Core\CustomType\CustomType::registerTaxonomy()
     */
    final public function tify_custom_taxonomy_register()
    {
        return CustomType::registerTaxonomy(
            $this->getCat(),
            $this->getAttr('category', [])
        );
    }

    /**
     * Déclaration de composants
     *
     * @return void
     */
    final public function tify_components_register()
    {
        Components::register('CustomColumns');
    }
        
    /**
     * Personnalisation des colonnes de l'interface d'administration
     *
     * @return void
     */
    final public function tify_custom_columns_register()
    {
        // Icône représentative de catégorie
        CustomColumns::register(
            'Icon',
            [],
            'taxonomy',
            $this->getCat()
        );

        // Ordre d'affichage de catégorie
        CustomColumns::register(
            'Order',
            [],
            'taxonomy',
            $this->getCat()
        );
    }
    
    /**
     *
     */
    public function tify_taboox_register_box()
    {
        Taboox::registerBox(
            'edit-'. $this->getCat(),
            [
                'title'         => __('Réglages de la catégorie de produits', 'tify'),
                'object_type'   => 'taxonomy',
                'object_name'   => $this->getCat()
            ]
        );
    }
    
    /**
     * 
     */
    public function tify_taboox_register_node()    
    {
        Taboox::registerNode(
            'edit-'. $this->getCat(),
            [
                'id'    => $this->getCat() .'--icon',
                'title' => __('Icône représentative', 'tify'),
                'cb'    => 'tiFy\Core\Taboox\Taxonomy\Icon\Admin\Icon'
            ]
        );
        tify_taboox_register_node( 'edit-'. $this->getCat(), array( 'id' => $this->getCat() .'--order', 'title' => __( 'Ordre d\'affichage', 'tify' ), 'cb' => 'tiFy\Core\Taboox\Taxonomy\Order\Admin\Order' ) );
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
        <?php echo $this->getCatAttr('singular', _x('catégorie de produits', 'taxonomy singular name', 'tify'));?>
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
            'taxonomy'    => 'tifyshopcat-' . $this->getId(),
            'object_type' => [$this->getId()],
            'gender'      => true,
            'label'       => _x('Catégories de produits', 'taxonomy general name', 'tify'),
            'plural'      => _x('catégories de produits', 'taxonomy plural name', 'tify'),
            'singular'    => _x('catégorie de produits', 'taxonomy singular name', 'tify')
        ];
        $attrs['category'] = isset($attrs['category']) ? \wp_parse_args($attrs['category'], $defaults_category) : $defaults_category;

        if (is_string($attrs['category']['object_type'])) :
            $attrs['category']['object_type'] = (array)$attrs['category']['object_type'];
        endif;

        if (!in_array($this->getId(), $attrs['category']['object_type'])) :
            $attrs['category']['object_type'][] = $this->getId();
        endif;

        return $attrs;
    }
    
    /**
     * CONTROLEURS
     */
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