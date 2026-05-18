<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'source'           => $this->source,
            'monthly_cost'     => (float) $this->monthly_cost,
            'monthly_deadline' => $this->monthly_deadline,
            'total_tenor'      => $this->total_tenor,
            'total_paid'       => (float) ($this->total_paid ?? 0),
            'remaining'        => round(
                ($this->monthly_cost * $this->total_tenor) - ($this->total_paid ?? 0),
                2
            ),
            'created_at'       => $this->created_at?->toDateTimeString(),
        ];
    }
}
