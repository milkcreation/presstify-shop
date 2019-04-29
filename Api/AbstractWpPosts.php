<?php

namespace tiFy\Plugins\Shop\Api;

use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use League\Fractal\Manager as DataManager;
use League\Fractal\Resource\Collection;
use tiFy\Contracts\Kernel\QueryCollection;
use tiFy\Contracts\Http\Request;
use tiFy\Support\DateTime;
use tiFy\Support\ParamsBag;
use tiFy\Plugins\Shop\Api\FractalArraySerializer as DataSerializer;
use tiFy\Plugins\Shop\ShopResolverTrait;

class AbstractWpPosts extends ParamsBag
{
    use ShopResolverTrait;

    /**
     * Instance du gestionnaire de données.
     * @var DataManager
     */
    protected $manager;

    /**
     * Identifiant de qualification de l'élément à traité.
     * @var int
     */
    protected $id = 0;

    /**
     * Nombre d'élément par page par défaut.
     * @var int
     */
    protected $per_page = 20;

    /**
     * Statut par défaut.
     * @var string
     */
    protected $status = 'publish';

    /**
     * Liste des statuts disponibles.
     * @var array
     */
    protected $statuses = [
        'publish',
        'future',
        'draft',
        'pending',
        'private',
        'trash',
        'auto-draft',
        'inherit'
    ];

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        $this->manager = (new DataManager())->setSerializer(new DataSerializer());
    }

    /**
     * @inheritdoc
     */
    public function endpointGet($id = 0)
    {
        $this->id = ($id instanceof ServerRequestInterface) ? 0 : $id;

        $items = $this->getItems(request());

        $per_page = $this->get('query_args.posts_per_page');

        $headers = $this->id
            ? []
            : [
                'total'        => $items->count(),
                'total-founds' => $items->getFounds(),
                'total-pages'  => $per_page<0
                    ? $items->getFounds() : ceil($items->getFounds() / $per_page)
            ];

        if (request()->get('raw')) {
            $body = $items->all();
        } else {
            $resources = new Collection($items, [$this, 'setItem']);
            $body = $this->getManager()->createData($resources)->toArray();
        }
        return compact('body', 'headers');
    }

    /**
     * @inheritdoc
     */
    public function endpointPost($id = 0)
    {
        $headers = [];
        $body = [];

        return compact('body', 'headers');
    }

    /**
     * {@inheritdoc}
     *
     * @param Request $request
     *
     * @return array|QueryCollection
     */
    public function getItems(Request $request)
    {
        $this->set($request->all())->parse();

        return $this->shop()->orders()->getCollection($this->get('query_args', []));
    }

    /**
     * @inheritdoc
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        if ($this->id) {
            $this->set('query_args.p', $this->id);
        } else {
            $this->set('query_args.paged', $this->parsePage());

            $this->set('query_args.posts_per_page', $this->parsePerPage());

            $this->set('query_args.order', $this->parseOrder());

            $this->set('query_args.orderby', $this->parseOrderBy());

            $this->set('query_args.post_status', $this->parseStatus());

            $date_query = [];
            if ($after = $this->parseAfter()) {
                $date_query['after'] = $after;
            }
            if ($before = $this->parseBefore()) {
                $date_query['before'] = $before;
            }
            if ($date_query) {
                $date_query += [
                    'column' => 'post_date',
                    'inclusive' => true
                ];
                $this->set('query_args.date_query.0', $date_query);
            }
        }
    }

    /**
     * Traitement de la date de début.
     *
     * @return array
     */
    public function parseAfter()
    {
        if ($start = request()->get('after', '')) {
            $start = DateTime::parse($start, DateTime::getGlobalTimeZone())->toDateString();
        }
        return $start;
    }

    /**
     * Traitement de la date de fin.
     *
     * @return array
     */
    public function parseBefore()
    {
        if ($end = request()->get('before', '')) {
            $end = DateTime::parse($end, DateTime::getGlobalTimeZone())->toDateString();
        }
        return $end;
    }

    /**
     * Traitement du nombre d'élément par page.
     *
     * @return int
     */
    public function parsePerPage()
    {
        if ($per_page = request()->get('per_page', 0)) {
            $per_page = intval($per_page);
        }
        return $per_page ?: $this->per_page;
    }

    /**
     * Traitement de la page courante.
     *
     * @return int
     */
    public function parsePage()
    {
        if ($page = request()->get('page', 0)) {
            $page = absint($page);
        }
        return $page ?: 1;
    }

    /**
     * Traitement de l'ordre de tri.
     *
     * @return string ASC|DESC
     */
    public function parseOrder()
    {
        $order = strtoupper(request()->get('order', ''));

        return in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
    }

    /**
     * Traitement de l'attribut d'ordonnacement.
     *
     * @return string
     */
    public function parseOrderBy()
    {
        return request()->get('orderby', 'ID');
    }

    /**
     * Traitement de la liste des statuts de commande.
     *
     * @return array
     */
    public function parseStatus()
    {
        if ($status = request()->get('status', '')) {
            $status = ($status === 'any')
                ? $this->statuses
                : array_unique(array_map('trim', explode(',', $status)));
            $status = array_intersect($status, $this->statuses);
        } else {
            $status = Arr::wrap($this->status);
        }
        return $status;
    }
}