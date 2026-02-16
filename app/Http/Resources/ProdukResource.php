<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_produk'    => $this->id_produk,
            'kode_produk'  => $this->kode_produk,
            'nama_produk'  => $this->nama_produk,
            'merk'         => $this->merk,
            'id_kategori'  => $this->id_kategori,
            'harga_beli'   => $this->harga_beli,
            'harga_jual'   => $this->harga_jual,
            'diskon'       => $this->diskon,
            'stok'         => $this->stok,
            'keuntungan'   => $this->keuntungan,
            'kategori'     => new KategoriResource($this->whenLoaded('kategori')),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
