<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PenjualanDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_penjualan_detail' => $this->id_penjualan_detail,
            'id_penjualan'        => $this->id_penjualan,
            'id_produk'           => $this->id_produk,
            'harga_jual'          => $this->harga_jual,
            'jumlah'              => $this->jumlah,
            'diskon'              => $this->diskon,
            'subtotal'            => $this->subtotal,
            'produk'              => new ProdukResource($this->whenLoaded('produk')),
        ];
    }
}
