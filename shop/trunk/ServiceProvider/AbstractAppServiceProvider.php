<?php

namespace tiFy\Plugins\Shop\ServiceProvider;

use Illuminate\Support\Arr;
use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\Exception\NotFoundException;
use League\Container\ServiceProvider\AbstractServiceProvider as LeagueAbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use LogicException;
use ReflectionFunction;
use ReflectionException;
use tiFy\Apps\AppTrait;

abstract class AbstractAppServiceProvider extends LeagueAbstractServiceProvider implements BootableServiceProviderInterface
{
    use AppTrait;

    /**
     * Classe de rappel du conteneur d'injection.
     * @var Container
     */
    protected $container;

    /**
     * Liste des identifiants de qualification de services fournis.
     * @internal requis. Tous les alias de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [];

    /**
     * Cartographie des controleurs de service fournis.
     * @internal optionnel. Etablis la relation entre les alias de services fournis (provides) et le controleur à instancier.
     * Par défaut, lorsque le controleur par défaut n'est pas renseigné, c'est l'alias du service lui-même qui qualifie le controleur.
     * @var array
     */
    protected $defaults_controller = [];

    /**
     * Cartographie des alias de service fournis.
     * @internal requis. Etabli la correspondances entre l'identifiant de qualification d'un service et son alias réel de service fournis.
     * Toutes les correspondances de services doivent être renseignées.
     * @var array
     */
    protected $aliases_map = [];

    /**
     * Listes des services déclarés, instanciés au démarrage.
     * @var array
     */
    protected $bootable = [];

    /**
     * Liste des services déclarés, instanciés de manière différés.
     * @var array
     */
    protected $deferred = [];

    /**
     * Cartographie des controleurs des services à traiter.
     * @var array
     */
    protected $controllers_map = [];

    /**
     * Cartographie des variables passé en arguments dans les services.
     * @var array
     */
    protected $arguments_map = [];

    /**
     * Cartographie des attributs (controller&args) des personnalisations définies.
     * @var array
     */
    protected $customs_map = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $customs Liste des attributs de personnalisation.
     *
     * @return void
     */
    public function __construct($customs = [])
    {
        foreach($customs as $category => $custom) :
            foreach($custom as $name => $attr) :
                $this->setMapCustom("{$category}.{$name}", $attr);
            endforeach;
        endforeach;
    }

    /**
     * Traitement de la cartographie des services déclarés.
     *
     * @return void
     */
    public function map()
    {
        foreach ($this->aliases_map as $category => $alias_map) :
            foreach ($alias_map as $name => $provide) :
                $key = "{$category}.{$name}";
                if ($this->isMapController($key)):
                    continue;
                endif;

                $controller = $this->getMapController($key, $this->getDefault($key));

                $this->setMapController($key, $controller);
            endforeach;
        endforeach;
    }

    /**
     * Déclaration des services instanciés au démarrage.
     *
     * @return void
     */
    public function boot()
    {
        $this->map();

        if ($this->bootable) :
            foreach ($this->bootable as $category => $controllers) :
                foreach ($controllers as $name) :
                    $key = "{$category}.{$name}";
                    $this->addContainer($key, $this->getMapArgs($key));
                endforeach;
            endforeach;
        endif;
    }

    /**
     * Déclaration des services instanciés de manière différées.
     *
     * @return void
     */
    public function register()
    {
        if ($this->deferred) :
            foreach ($this->deferred as $category => $controllers) :
                foreach ($controllers as $name) :
                    $key = "{$category}.{$name}";
                    $this->addContainer($key, $this->getMapArgs($key));
                endforeach;
            endforeach;
        endif;
    }

    /**
     * Récupération de la classe de rappel du conteneur d'injection utilisé par le fournisseur de service.
     *
     * @return ContainerInterface|Container
     */
    public function getContainer()
    {
        return parent::getContainer();
    }

    /**
     * Récupération du controleur de service par défaut
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une récupération en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     *
     * @return mixed
     *
     * @throws LogicException
     */
    public function getDefault($key)
    {
        try {
            $alias = $this->getAlias($key);
        } catch (LogicException $e) {
            \wp_die($e->getMessage(), __('Récupération du controleur par défaut impossible', 'tify'), 500);
            exit;
        }

        $controller = isset($this->defaults_controller[$alias]) ? $this->defaults_controller[$alias] : $alias;

        if (!is_callable($controller) && (is_string($controller) && !class_exists($controller))) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de service qualifié par l\'identifiant <b>%s</b> ne peut être instancié.',
                        'tify'),
                    $key
                )
            );
        endif;

        return $controller;
    }

    /**
     * Récupération de l'alias d'un service.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une récupération en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     *
     * @return string
     *
     * @throws LogicException
     */
    public function getAlias($key)
    {
        if ($alias = Arr::get($this->aliases_map, $key, '')) :
            return $alias;
        endif;

        if ($this->provides($alias)) :
            return $alias;
        endif;

        throw new LogicException(
            sprintf(
                __(
                    'Le service qualifié par l\'identifiant <b>%s</b> n\'a pas de correspondance parmis les services disponibles.',
                    'tify'
                ),
                $key
            )
        );
    }

    /**
     * Déclaration d'un conteneur d'injection de service selon ses attributs enregistrés.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une définition en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     * @param array $args Liste des variable passés en argument au controleur.
     *
     * @return bool
     */
    public function addContainer($key, $args = [])
    {
        if (!$controller = $this->getMapController($key)) :
            return false;
        endif;

        try {
            $alias = $this->getAlias($key);
        } catch (LogicException $e) {
            \wp_die($e->getMessage(), __('Déclaration du controleur d\injection impossible', 'tify'), 500);
            exit;
        }

        if ($this->isClosure($controller)) :
            $this->getContainer()->add(
                $alias,
                call_user_func_array($controller, $args)
            );
        else :
            $this->getContainer()->add(
                $alias,
                $controller
            )
                ->withArguments($args);
        endif;

        return true;
    }

    /**
     * Vérifie si un controleur est une fonction anonyme.
     *
     * @return bool
     */
    public function isClosure($controller)
    {
        try {
            $reflection = new ReflectionFunction($controller);
            return $reflection->isClosure();
        } catch (ReflectionException $e) {
            return false;
        }
    }

    /**
     * Déclaration de personnalisation de controleur de service.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une définition en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     * @param mixed $value Valeur de la personnalisation.
     *
     * @return self
     *
     * @throws LogicException
     */
    public function setMapCustom($key, $value)
    {
        $controller = '';
        $args = [];

        if(is_string($value) || is_callable($value) || is_object($value)) :
            $controller = $value;
        elseif (is_array($value) && (count($value) === 2)) :
            list($controller, $args) = $value;
        else :
            throw new LogicException(
                sprintf(
                    __(
                        'La définition de la personnalisation du service <b>%s</b> n\'est pas conforme.',
                        'tify'
                    ),
                    $key
                )
            );
        endif;

        if ($controller) :
            $this->setMapController($key, $controller);
        endif;

        if ($args) :
            $this->setMapArgs($key, $args);
        endif;

        $this->customs_map = Arr::add($this->customs_map, $key, [$controller, $args]);

        return $this;
    }

    /**
     * Déclaration d'un controleur de service s'il n'existe pas encore.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une définition en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     * @param mixed $controller Définition du controleur.
     *
     * @return self
     */
    public function setMapController($key, $controller)
    {
        $this->controllers_map = Arr::add($this->controllers_map, $key, $controller);

        return $this;
    }

    /**
     * Déclaration d'une liste de variables à passer en argument dans le service si elle n'existe pas encore.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une définition en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     * @param array $args Liste des variables passés en argument du service.
     *
     * @return self
     */
    public function setMapArgs($key, $args)
    {
        $this->arguments_map = Arr::add($this->arguments_map, $key, $args);

        return $this;
    }

    /**
     * Récupération des attributs de personnalisation.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une récupération en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     * @param mixed $attr Attribut à récupérer. void (tous par défaut)|controller|args.
     *
     * @return false|mixed
     */
    public function getMapCustom($key, $attr = null)
    {
        $attrs = Arr::get($this->customs_map, $key, ['', []]);

        switch($attr) :
            default:
                return $attrs;
                break;
            case 'controller':
                return $attrs[0];
                break;
            case 'args':
                return $attrs[1];
                break;
        endswitch;
    }

    /**
     * Récupération d'un controleur de service déclaré.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une récupération en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     * @param mixed $default Valeur de retour par defaut.
     *
     * @return mixed
     */
    public function getMapController($key, $default = null)
    {
        return Arr::get($this->controllers_map, $key, $default);
    }

    /**
     * Récupération d'une liste de variable à passer en argument dans le service.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une récupération en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     * @param array $default Valeur de retour par defaut.
     *
     * @return array
     */
    public function getMapArgs($key, $default = [])
    {
        return (array)Arr::get($this->arguments_map, $key, $default);
    }

    /**
     * Vérifie si un controleur de service esr déclaré.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une récupération en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     *
     * @return bool
     */
    public function isMapController($key)
    {
        return Arr::exists($this->controllers_map, $key);
    }

    /**
     * Récupération ponctuelle d'une instance de service déclaré.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une récupération en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     *
     * @return object
     */
    public function get($key, $args = [])
    {
        try {
            $alias = $this->getAlias($key);
        } catch (LogicException $e) {
            \wp_die($e->getMessage(), __('Récupération de l\'instance impossible', 'tify'), 500);
            exit;
        }

        try {
            return $this->getContainer()->get($alias, $args);
        } catch (NotFoundException $e) {
            \wp_die($e->getMessage(), __('Récupération de l\'instance impossible', 'tify'), 500);
            exit;
        }
    }

    /**
     * Déclaration ponctuelle d'un service au travers du fournisseur de service, s'il nexiste pas encore.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une récupération en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $key Identifiant de qualification du service.
     * @param mixed $controller Définition du controleur.
     * @param array $args Liste des variable passés en argument au controleur.
     *
     * @return bool
     */
    public function add($key, $controller, $args = [])
    {
        if ($this->isMapController($key)) :
            return false;
        endif;

        $controller = $this->getMapController($key, $controller);

        array_push($this->provides, $controller);
        $this->aliases_map = Arr::add($this->aliases_map, $key, $controller);

        $this->setMapController($key, $controller);
        $this->setMapArgs($key, $args);

        return $this->addContainer($key, $args);
    }
}