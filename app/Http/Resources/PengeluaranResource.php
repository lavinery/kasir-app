<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PengeluaranResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_pengeluaran' => $this->id_pengeluaran,
            'deskripsi'      => $this->deskripsi,
            'nominal'        => $this->nominal,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
