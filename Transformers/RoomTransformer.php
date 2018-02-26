<?php

namespace App\Transformers;


use League\Fractal\TransformerAbstract;

class RoomTransformer extends TransformerAbstract
{

    /**
     * A Fractal transformer.
     *
     * @param $room
     * @return array
     */
    public function transform($room)
    {
        return [
            'id'          => $room->id,
            'room_number' => $room->rome_number,
            'length'      => $room->length,
            'width'       => $room->width,
            'floor'       => $room->floor,
            'rent'        => $room->rent . 'افغانی',
            'capacity'    => $room->capacity . 'نفر'
        ];
    }
}
