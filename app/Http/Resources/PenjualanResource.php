<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PenjualanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_penjualan' => $this->id_penjualan,
            'id_member'    => $this->id_member,
            'total_item'   => $this->total_item,
            'total_harga'  => $this->total_harga,
            'diskon'       => $this->diskon,
            'bayar'        => $this->bayar,
            'diterima'     => $this->diterima,
            'id_user'      => $this->id_user,
            'member'       => new MemberResource($this->whenLoaded('member')),
            'user'         => $this->whenLoaded('user', function () {
                return [
                    'id'   => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'detail'       => PenjualanDetailResource::collection($this->whenLoaded('detail')),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
