<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface ShopInterface extends ShopResolverInterface
{
    /**
     * Résolution de service fournis.
     *
     * @param string $alias
     * @param array ...$args Liste d'arguments dynamiques complémentaires.
     *
     * @return mixed
     */
    public function resolve(string $alias, ...$args);

    /**
     * Récupération du chemin absolu vers une ressource.
     *
     * @param string $path Chemin relatif vers un sous élément.
     *
     * @return string
     */
    public function resourcesDir(string $path = ''): string;

    /**
     * Récupération de l'url absolue vers une ressource.
     *
     * @param string $path Chemin relatif vers un sous élément.
     *
     * @return string
     */
    public function resourcesUrl(string $path = ''): string;
}