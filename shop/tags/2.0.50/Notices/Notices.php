<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Notices;

use LogicException;
use tiFy\Plugins\Shop\Contracts\{Notices as NoticesContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Proxy\Partial;

class Notices implements NoticesContract
{
    use ShopAwareTrait;

    /**
     * Liste des messages de notification à afficher
     * @var array
     */
    protected $notices = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);

        $this->boot();

        add_action('wp_loaded', function () {
            $this->notices = $this->shop()->session()->get('notices', []);
        });
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * Affichage des message de notification
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->display();
    }

    /**
     * @inheritDoc
     */
    public function add($message, $type = 'success'): void
    {
        if (!did_action('wp_loaded')) {
            throw new LogicException(
                __(
                    'L\'ajout de message de notification ne devrait pas être fait ' . '
                    à ce moment de l\'exécution de votre code',
                    'tify'
                ),
                500
            );
        }
        $type = strtolower($type);
        $type = ($type === 'notice') ? 'success' : $type;

        if (!isset($this->notices[$type])) {
            $this->notices[$type] = [];
        }
        $this->notices[$type][] = $message;

        $this->shop()->session()->put('notices', $this->notices);
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->notices = [];
        $this->shop()->session()->put('notices', []);
    }

    /**
     * @inheritDoc
     */
    public function display(): string
    {
        if (!did_action('template_redirect')) {
            throw new LogicException(
                __(
                    'L\'affichage des messages de notifications ne devrait pas être fait ' . '
                    à ce moment de l\'execution de votre code',
                    'tify'
                ),
                500
            );
        }

        if (!$this->notices) {
            return '';
        }

        $output = "";
        foreach ($this->notices as $type => $messages) {
            foreach ($messages as $content) {
                $output .= Partial::get('notice', compact('type', 'content'));
            }
        }

        $this->clear();

        return $output;
    }
}