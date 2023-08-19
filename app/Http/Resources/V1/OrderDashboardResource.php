<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_earnings' => $this->resource['totalEarnings'],
            'potential_earnings' => $this->resource['potentialEarnings'],
            'total_orders' => $this->resource['totalOrders'],
            'chart_data' => $this->resource['chartData'],
            'orders' => $this->resource['orders'],
        ];
    }
}
