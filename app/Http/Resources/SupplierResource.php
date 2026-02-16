<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_supplier' => $this->id_supplier,
            'nama'        => $this->nama,
            'telepon'     => $this->telepon,
            'alamat'      => $this->alamat,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
