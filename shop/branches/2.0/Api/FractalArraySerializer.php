<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Api;

use League\Fractal\Serializer\ArraySerializer;

class FractalArraySerializer extends ArraySerializer
{
    /**
     * @inheritDoc
     */
    public function collection($resourceKey, array $data)
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function item($resourceKey, array $data)
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }
}