<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->profile->first_name,
            'last_name' => $this->profile->last_name,
            'email' => $this->email,
            'birthed_at' => $this->profile->birthed_at,
            'email_verified_at' => $this->email_verified_at,
        ];
    }
}
