<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\OrderDashboardRequest;
use App\Http\Resources\V1\OrderDashboardResource;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderDashboardController extends Controller
{
    private Collection $orderStatuses;

    public function __construct()
    {
        $this->orderStatuses = OrderStatus::all();
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(OrderDashboardRequest $request)
    {
        $inputs = $request->validated();

        $totalEarnings = $this->calculateTotalEarnings($inputs);
        $potentialEarnings = $this->calculatePotentialEarnings($inputs);
        $chartData = $this->getChartData($inputs);
        $orders = $this->getOrders($inputs);

        return new OrderDashboardResource([
            'totalEarnings' => $totalEarnings,
            'potentialEarnings' => $potentialEarnings,
            'totalOrders' => $orders->total(),
            'chartData' => $chartData,
            'orders' => $orders
        ]);
    }

    private function calculateTotalEarnings(array $inputs): string
    {
        ['startDate' => $startDate, 'endDate' => $endDate] = $this->determineDates($inputs);

        $amount = $this->ordersQuery($inputs, $startDate, $endDate)
            ->clone()
            ->where('order_status_id', $this->orderStatuses->where('title', 'Paid')->first()->id)
            ->sum('amount');

        return format_number($amount);
    }

    private function calculatePotentialEarnings(array $inputs): string
    {
        ['startDate' => $startDate, 'endDate' => $endDate] = $this->determineDates($inputs);

        $amount = $this->ordersQuery($inputs, $startDate, $endDate)
            ->clone()
            ->whereIn('order_status_id', $this->orderStatuses->whereIn('title', ['Open', 'Pending payment'])->pluck('id')->toArray())
            ->sum('amount');

        return format_number($amount);
    }

    private function getChartData(array $inputs): array
    {
        $range = $this->determineRange($inputs);
        ['startDate' => $startDate, 'endDate' => $endDate] = $this->determineDates($inputs);

        $query = $this->ordersQuery($inputs, $startDate, $endDate)->clone();

        if($range === 'today') {
            $query->select([
                DB::raw('HOUR(created_at) as created_at_casted'),
                DB::raw('SUM(amount) as amount'),
            ]);
        } else if($range === 'monthly') {
            $query->select([
                DB::raw('DATE(created_at) as created_at_casted'),
                DB::raw('SUM(amount) as amount'),
            ]);
        } else if($range === 'yearly') {
            $query->select([
                DB::raw('EXTRACT(YEAR_MONTH FROM created_at) created_at_casted'),
                DB::raw('SUM(amount) as amount'),
            ]);
        }

        $orders = $query
            ->groupBy('created_at_casted')
            ->orderBy('created_at_casted')
            ->get();

        $period = CarbonPeriod::create($startDate->startOfDay(), '1 hour', $endDate->endOfDay());
        if($range === 'monthly') {
            $period = CarbonPeriod::create($startDate->startOfMonth(), '1 day', $endDate->endOfMonth());
        } else if($range === 'yearly') {
            $period = CarbonPeriod::create($startDate->startOfYear(), '1 month', $endDate->endOfYear());
        }

        $labelFormat = match ($range) {
            'today' => 'g A',
            'monthly' => 'j.n',
            'yearly' => 'M y',
        };

        $valueFormat = match ($range) {
            'today' => 'G',
            'monthly' => 'Y-m-d',
            'yearly' => 'Ym',
        };

        return collect($period->toArray())->map(function($day) use ($orders, $labelFormat, $valueFormat) {
            return [
                'label' => $day->format($labelFormat),
                'value' => $orders->where('created_at_casted', $day->format($valueFormat))->first()?->amount ?: number_format('0', 2),
            ];
        })->toArray();
    }

    private function getOrders(array $inputs): LengthAwarePaginator
    {
        ['startDate' => $startDate, 'endDate' => $endDate] = $this->determineDates($inputs);
        $orderBy = $inputs['sortBy'] ?? 'orders.created_at';
        $orderByDirection = $inputs['desc'] ? 'desc' : 'asc';

        return $this->ordersQuery($inputs, $startDate, $endDate)
            ->clone()
            ->select([
                'orders.uuid',
                'orders.amount',
                DB::raw('JSON_LENGTH(orders.products) as ordered_products'),
                DB::raw('order_statuses.title as status'),
                DB::raw('CONCAT_WS(" ", users.first_name, users.last_name) as customer'),
            ])
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->join('order_statuses', 'order_statuses.id', '=', 'orders.order_status_id')
            ->orderBy($orderBy, $orderByDirection)
            ->paginate(
                perPage: $inputs['limit'],
                page: $inputs['page'],
            );
    }

    private function ordersQuery(array $inputs, Carbon $startDate, Carbon $endDate)
    {
        $ordersQuery = Order::query();

        $ordersQuery->whereBetween('orders.created_at', [
            $startDate,
            $endDate
        ]);

        return $ordersQuery;
    }

    private function determineRange(array $inputs): string
    {
        if(isset($inputs['fixRange']))
            return $inputs['fixRange'];

        $dateRange = $inputs['dateRange'];
        $fromDate = Carbon::parse($dateRange['from']);
        $toDate = Carbon::parse($dateRange['to']);

        $days = $fromDate->diffInDays($toDate);

        if($days < 1) {
            return 'today';
        }else if($days <= 30) {
            return 'monthly';
        }

        return 'yearly';
    }

    private function determineDates(array $inputs): array
    {
        if(isset($inputs['fixRange']) && $inputs['fixRange'] === 'today') {
            return [
                'startDate' => Carbon::now()->startOfDay(),
                'endDate' => Carbon::now()->endOfDay(),
            ];
        } else if(isset($inputs['fixRange']) && $inputs['fixRange'] === 'monthly') {
            return [
                'startDate' => Carbon::now()->startOfMonth(),
                'endDate' => Carbon::now()->endOfMonth(),
            ];
        } else if(isset($inputs['fixRange']) && $inputs['fixRange'] === 'yearly') {
            return [
                'startDate' => Carbon::now()->startOfMonth(),
                'endDate' => Carbon::now()->endOfMonth(),
            ];
        }

        return [
            'startDate' => Carbon::parse($inputs['dateRange']['from'])->startOfDay(),
            'endDate' => Carbon::parse($inputs['dateRange']['to'])->endOfDay(),
        ];
    }
}
