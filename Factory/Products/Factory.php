<?php
namespace tiFy\Plugins\Shop\Factory\Products;

use tiFy\Core\CustomType\CustomType;

abstract class Factory extends \tiFy\App\FactoryConstructor
{
    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct($id, $attrs = [])
    {
        parent::__construct($id, $attrs);

        // Déclaration des événements de déclenchement
        $this->tFyAppActionAdd('tify_custom_post_type_register');
    }
    
    /**
     * DECLENCHEURS
     */
    /**
     * Déclaration des types de posts personnalisés des gammes de produits
     *
     * @return \tiFy\Core\CustomType\CustomType::registerPostType()
     */
    final public function tify_custom_post_type_register()
    {
        CustomType::registerPostType(
            $this->getId(),
            $this->Attrs
        );
    }
    
    /**
     * CONTROLEURS
     */
    /**
     * Traitement de arguments de configuration
     *
     * @param array $attrs
     *
     * @return array
     */
    protected function parseAttrs($attrs = [])
    {
        if (empty($attrs['label'])) :
            $attrs['label'] = _x(sprintf('Produits de la gamme %s', $this->getId()), 'post type general name', 'tify');
        endif;

        if (empty($attrs['plural'])) :
            $attrs['plural'] = _x(sprintf('Produits de la gamme %s', $this->getId()), 'post type plural name', 'tify');
        endif;

        if (empty($attrs['singular'])) :
            $attrs['singular'] = _x(sprintf('Produit de la gamme %s', $this->getId()), 'post type singular name', 'tify');
        endif;

        if (empty($attrs['menu_icon'])) :
            $attrs['menu_icon'] = 'dashicons-products';
        endif;

        return $attrs;
    }

    /**
     * Vérifie s'il s'agit d'une gamme de produit unique
     *
     * @return bool
     */
    final public function hasCat()
    {
        $this->getAttr('category', false);
    }
}