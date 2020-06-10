<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Model;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /**
     * @var string
     */
    protected $table = 'tify_shop_order_items';

    /**
     * @var string
     */
    protected $primaryKey = 'order_item_id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['order_item_name', 'order_item_type', 'order_id'];
}