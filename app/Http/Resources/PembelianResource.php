<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PembelianResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_pembelian' => $this->id_pembelian,
            'id_supplier'  => $this->id_supplier,
            'total_item'   => $this->total_item,
            'total_harga'  => $this->total_harga,
            'diskon'       => $this->diskon,
            'bayar'        => $this->bayar,
            'supplier'     => new SupplierResource($this->whenLoaded('supplier')),
            'detail'       => PembelianDetailResource::collection($this->whenLoaded('detail')),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
