<?php
/**
 * Fiche produit - Option d'achat.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\View\ViewController $this
 * @var \WP_Post $post
 * @var tiFy\Plugins\Shop\Products\Product $product
 * @var tiFy\Plugins\Shop\Contracts\ProductPurchasingOption $option
 */
echo "{$option->getLabel()} :  {$option->getValue()}";