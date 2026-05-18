<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     @OA\Property(property="id",    type="integer", example=1),
 *     @OA\Property(property="name",  type="string",  example="Ichsan"),
 *     @OA\Property(property="email", type="string",  example="ichsan@keluargakas.app"),
 *     @OA\Property(property="role",  type="string",  enum={"admin","user"}, example="user"),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'role'       => $this->role,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
