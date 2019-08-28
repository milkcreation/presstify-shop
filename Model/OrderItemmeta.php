<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Model;

use Corcel\Model\Meta\Meta;
use tiFy\Wordpress\Database\Concerns\MetaAwareTrait;

class OrderItemmeta extends Meta
{
    use MetaAwareTrait;

    /**
     * @var string
     */
    protected $table = 'tify_shop_order_itemmeta';

    /**
     * @var array
     */
    protected $fillable = ['meta_key', 'meta_value', 'order_item_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}