<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface CartLine extends ParamsBag, ShopAwareTrait
{
    /**
     * Récupération de l'instance du panier associé.
     *
     * @return Cart|null
     */
    public function cart(): ?Cart;

    /**
     * Nom du champ de modification d'un attribut dans le panier.
     *
     * @param string $attribute_name Nom de l'attribut du champ.
     *
     * @return string
     */
    public function cartFieldName(string $attribute_name): string;

    /**
     * Récupération de l'identifiant de qualification de l'article dans le panier.
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Récupération du prix de vente.
     *
     * @return float
     */
    public function getPrice(): float;

    /**
     * Récupération de l'affichage HTML du prix de vente.
     *
     * @return string
     */
    public function getPriceHtml(): string;

    /**
     * Indique si le prix de vente tient compte de la taxe.
     *
     * @return string|null
     */
    public function getPriceIncludesTax(): ?string;

    /**
     * Récupération des données du produit associé à l'article du panier.
     *
     * @return Product|null
     */
    public function getProduct(): ?Product;

    /**
     * Récupération de l'identifiant de qualification du produit associé à l'article du panier.
     *
     * @return int
     */
    public function getProductId(): int;

    /**
     * Récupération des options d'achat par produit.
     *
     * @return array
     */
    public function getPurchasingOptions(): array;

    /**
     * Récupération de la quantité du produit associé à l'article du panier
     *
     * @return int
     */
    public function getQuantity(): int;

    /**
     * @return float
     */
    public function getSubtotal(): float ;

    /**
     * @return float
     */
    public function getSubtotalTax(): float;

    /**
     * @return float
     */
    public function getTax(): float;

    /**
     * @return string|null
     */
    public function getTaxable(): ?string;

    /**
     * @return string
     */
    public function getTaxClass(): string;

    /**
     * @return array
     */
    public function getTaxes(): array;

    /**
     * @return array
     */
    public function getTaxRates(): array;

    /**
     * @return float
     */
    public function getTotal(): float;

    /**
     * Vérifie si l'article nécessite une livraison.
     *
     * @return boolean
     */
    public function needShipping(): bool;

    /**
     * Url de suppression de l'article dans le panier d'achat.
     *
     * @return string
     */
    public function removeUrl(): string;

    /**
     * Définition du panier associé.
     *
     * @param Cart $cart
     *
     * @return static
     */
    public function setCart(Cart $cart): CartLine;
}