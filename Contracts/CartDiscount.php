<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface CartDiscount
{
    /**
     * Calcul du montant de la remise.
     *
     * @return float
     */
    public function calculate(): float;

    /**
     * Récupération de l'instance du panier associé.
     *
     * @return Cart|null
     */
    public function cart(): ?Cart;

    /**
     * Définition de la liste des paramètres par défaut.
     *
     * @return array
     */
    public function defaultsParams(): array;

    /**
     * Récupération du montant de la remise.
     *
     * @return float
     */
    public function getAmount(): float;

    /**
     * Récupération du seuil de déclenchement de la remise.
     *
     * @return float
     */
    public function getTrigger(): float;

    /**
     * Vérification de validité de la remise.
     *
     * @return boolean
     */
    public function isValid(): bool;

    /**
     * Récupération|Définition de paramètres|Instance du gestionnaire de paramètres.
     *
     * @param string|array|null $key Indice à récupérer|Liste à définir|null pour l'instance.
     * @param mixed $default Valeur de retour par défaut si $key est une chaîne de caractères.
     *
     * @return mixed|ParamsBag
     */
    public function params($key = null, $default = null);

    /**
     * Traitement à l'initialisation des paramètres.
     *
     * @return void
     */
    public function parseParams(): void;

    /**
     * Définition du montant de la remise.
     *
     * @return static
     */
    public function setAmount(float $amount): CartDiscount;

    /**
     * Définition du seuil de déclenchement de la remise.
     *
     * @return static
     */
    public function setTrigger(float $trigger): CartDiscount;
}