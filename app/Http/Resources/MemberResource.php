<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_member'   => $this->id_member,
            'kode_member' => $this->kode_member,
            'nama'        => $this->nama,
            'telepon'     => $this->telepon,
            'alamat'      => $this->alamat,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
