<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'amount'      => (float) $this->amount,
            'category'    => $this->category,
            'date'        => $this->date?->toDateString(),
            'description' => $this->description,
            // Generate URL dari disk aktif — otomatis sesuai RECEIPT_DISK di .env
            'receipt_url' => $this->receipt_url
                ? Storage::disk(config('filesystems.receipt_disk'))->url($this->receipt_url)
                : null,
            'debt'        => $this->whenLoaded('debt', fn () => [
                'id'     => $this->debt->id,
                'source' => $this->debt->source,
            ]),
            'created_at'  => $this->created_at?->toDateTimeString(),
        ];
    }
}
