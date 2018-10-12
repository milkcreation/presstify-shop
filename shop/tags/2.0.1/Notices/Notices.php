<?php

/**
 * @name Notices
 * @desc Gestion des messages de notification
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Notices
 * @version 1.1
 * @since 1.2.600
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Notices;

use tiFy\Partial\Partial;
use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\NoticesInterface;

class Notices extends AbstractShopSingleton implements NoticesInterface
{
    /**
     * Liste des messages de notification à afficher
     * @var array
     */
    protected $notices = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'wp_loaded',
            function() {
                $this->notices = $this->session()->get('notices', []);
            }
        );
    }

    /**
     * Affichage des message de notification
     *
     * @return string
     */
    public function __toString()
    {
        return $this->display();
    }

    /**
     * {@inheritdoc}
     */
    public function add($message, $type = 'success')
    {
        if (!did_action('wp_loaded')) :
            throw new LogicException(
                __('L\'ajout de message de notification ne devrait pas être fait à ce moment de l\'exécution de votre code', 'tify'),
                500
            );
        endif;

        if (!isset($this->notices[$type])) :
            $this->notices[$type] = [];
        endif;
        $this->notices[$type][] = $message;

        $this->session()->put('notices', $this->notices);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->notices = [];
        $this->session()->put('notices', []);
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        if (!did_action('template_redirect')) :
            throw new \LogicException(
                __('L\'affichage des messages de notifications ne devrait pas être fait à ce moment de l\'execution de votre code', 'tify'),
                500
            );
        endif;

        if (!$this->notices) :
            return '';
        endif;

        $output = "";
        foreach ($this->notices as $type => $messages) :
            foreach($messages as $content) :
                $output .= (string)partial('notice', compact('type', 'content'));
            endforeach;
        endforeach;

        $this->clear();

        return $output;
    }
}