<?php
namespace tiFy\Plugins\Shop;

class Factory extends \tiFy\App\FactoryConstructor
{
    /**
     * Classe de rappel des controleurs frère
     * @var \Pixvert\Shop\Session\Factory[]
     */
    protected $Siblings = [];

    /**
     * CONTROLEURS
     */
    /**
     * Récupération d'un controleur frère
     *
     * @param string $sibling_id Identifiant de qualification du controleur frère
     *
     * @return \Pixvert\Shop\Session\Factory
     */
    public function sibling($sibling_id)
    {
        if (!empty($this->Siblings[$sibling_id])) :
            return $this->Siblings[$sibling_id];
        elseif ($sibling_id === $this->getId()) :
            return $this;
        else :
            return $this->Siblings[$sibling_id] = Shop::get($sibling_id);
        endif;
    }
}