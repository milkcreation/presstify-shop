<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Addresses;

use Illuminate\Support\Str;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Plugins\Shop\Contracts\{Addresses, Address as AddressContract, Shop, User};
use tiFy\Plugins\Shop\ShopAwareTrait;

abstract class AbstractAddress implements AddressContract
{
    use ShopAwareTrait;

    /**
     * Identifiant de qualification.
     * @var string
     */
    protected $id = '';

    /**
     * Instance de la classe de gestion du formulaire.
     * @var FormFactory|false|null
     */
    protected $form;

    /**
     * Instance de la classe de l'utilisateur courant.
     * @var User
     */
    protected $user;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);

        $this->boot();

        add_action('init', function () {
            $this->user = $this->shop->users()->get();

            /**
             * Traitement de la liste des champs.
             * {@internal Ajout du préfixe aux identifiants de champ et récupération de la valeur.}
             */
            $attrs = $this->formAttrs();
            foreach ($attrs['fields'] as $slug => &$fattrs) {
                if (!isset($fattrs['name'])) {
                    $fattrs['name'] = $this->getId() . '_' . $slug;
                }

                if (!isset($fattrs['value'])) {
                    $method = 'get' . $this->getId() . Str::studly($slug);
                    $fattrs['value'] = $this->shop->session()->get($this->getId() . '.' . $slug)
                        ?: (method_exists($this->user, $method)
                            ? call_user_func([$this->user, $method])
                            : ''
                        );
                }
            }

            $attrs['addons']['shop.addresses.form-handler'] = ['controller' => $this];

            form()->register('ShopFormAddress-' . $this->getId(), $attrs);
        });
    }

    /**
     * @inheritDoc
     */
    public function addons(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function addresses(): Addresses
    {
        return $this->shop()->resolve('addresses');
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * @inheritDoc
     */
    public function buttons(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function callbacks(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        $fields = [];
        $defaults = $this->addresses()->defaultFields();

        foreach ($defaults as $slug => $attrs) {
            $fields[$slug] = $attrs;
        }

        return $fields;
    }

    /**
     * @inheritDoc
     */
    public function form(): ?FormFactory
    {
        if (is_null($this->form)) {
            $this->form = form()->get('ShopFormAddress-' . $this->getId());
        }

        return $this->form ?: null;
    }

    /**
     * @inheritDoc
     */
    public function formAttrs(): array
    {
        return [
            'attrs'     => [
                'id'    => 'FormShopAddress--' . $this->getId(),
                'class' => 'FormShopAddress FormShopAddress--' . $this->getId(),
            ],
            'addons'    => $this->addons(),
            'buttons'   => $this->buttons(),
            'fields'    => $this->fields(),
            'notices'   => $this->notices(),
            'options'   => $this->options(),
            'callbacks' => $this->callbacks(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id ?: Str::lower(class_info($this)->getShortName());
    }

    /**
     * @inheritDoc
     */
    public function notices(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function options(): array
    {
        return [];
    }
}