<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PembelianDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_pembelian_detail' => $this->id_pembelian_detail,
            'id_pembelian'        => $this->id_pembelian,
            'id_produk'           => $this->id_produk,
            'harga_beli'          => $this->harga_beli,
            'jumlah'              => $this->jumlah,
            'subtotal'            => $this->subtotal,
            'produk'              => new ProdukResource($this->whenLoaded('produk')),
        ];
    }
}
