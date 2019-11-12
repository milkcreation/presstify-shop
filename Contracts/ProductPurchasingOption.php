<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface ProductPurchasingOption extends ParamsBag, ShopAwareTrait
{
    /**
     * Intitulé de qualification.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Identifiant de qualification.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Classe de rappel du produit associé.
     *
     * @return Product
     */
    public function getProduct(): Product;

    /**
     * Récupération de la valeur de selection.
     *
     * @param mixed $default Valeur de retour par défaut
     *
     * @return mixed
     */
    public function getValue($default = null);

    /**
     * Intitulé de qualification.
     *
     * @return array
     */
    public function getValueList(): array;

    /**
     * Vérification d'activation de l'option d'achat.
     *
     * @return boolean
     */
    public function isActive(): bool;

    /**
     * Affichage d'une ligne de panier.
     *
     * @return string
     */
    public function renderCartLine(): string;

    /**
     * Affichage du champ de saisie.
     *
     * @return string
     */
    public function renderProduct(): string;

    /**
     * Définition de la valeur de selection.
     *
     * @param string $selected
     *
     * @return static
     */
    public function setSelected(string $selected): ProductPurchasingOption;
}