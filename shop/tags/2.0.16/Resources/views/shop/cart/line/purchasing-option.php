<?php
/**
 * Fiche produit - Option d'achat.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\View\ViewController $this
 * @var \WP_Post $post
 * @var tiFy\Plugins\Shop\Products\ProductItem $product
 * @var tiFy\Plugins\Shop\Contracts\ProductPurchasingOption $option
 */
?>

<?php echo $option->getLabel(); ?> : <?php echo $option->getValue(); ?>