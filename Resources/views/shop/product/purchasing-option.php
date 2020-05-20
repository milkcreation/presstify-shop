<?php
/**
 * Fiche produit - Option d'achat.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Plugins\Shop\ShopViewController $this
 * @var \WP_Post $post
 * @var tiFy\Plugins\Shop\Products\Product $product
 * @var tiFy\Plugins\Shop\Contracts\ProductPurchasingOption $option
 * @var array $field
 */
?><h3><?php echo $this->get('label'); ?></h3>
<?php echo field($this->get('field.type'), $this->get('field.args', []));