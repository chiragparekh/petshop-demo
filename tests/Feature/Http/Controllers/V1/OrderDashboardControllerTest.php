<?php

namespace Tests\Feature\Http\Controllers\V1;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use App\Services\JwtService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class OrderDashboardControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function setupAdminUserAndToken()
    {
        $adminUser = User::factory()->create([
            'is_admin' => 1
        ]);

        $token = app(JwtService::class)->generate($adminUser->uuid);

        return [
            'user' => $adminUser,
            'token' => $token
        ];
    }

    private function createPaidOrders(Carbon $startDate, Carbon $endDate, int $count = 5): Collection
    {
        $orderStatuses = OrderStatus::all();

        return Order::factory()->count($count)
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'created_at' => $this->faker->dateTimeBetween(
                        Carbon::now()->startOfDay(),
                        Carbon::now()->endOfDay(),
                    )
                ],
            ))
            ->create([
                'order_status_id' => OrderStatus::where('title', 'Paid')->first()->id
            ]);
    }

    private function createUnpaidOrders(Carbon $startDate, Carbon $endDate, int $count = 5): Collection
    {
        $orderStatuses = OrderStatus::all();

        return Order::factory()->count($count)
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'order_status_id' => $this->faker->randomElement(
                        $orderStatuses->whereIn('title', ['Open', 'Pending payment'])->pluck('id')->toArray()
                    ),
                    'created_at' => $this->faker->dateTimeBetween(
                        $startDate,
                        $endDate,
                    )
                ],
            ))
            ->create();
    }


    public function test_order_dashboard_validation_rules(): void
    {
        list('user' => $user, 'token' => $token) = $this->setupAdminUserAndToken();

        // test without passing any data
        $this->withToken($token)
            ->getJson('/api/v1/orders/dashboard')
            ->assertJsonValidationErrors([
                'dateRange',
                'fixRange'
            ]);

        // test with correct date range
        $data = http_build_query([
            'page' => 1,
            'limit' => 15,
            'sortBy' => 'status',
            'desc' => 0,
            'dateRange' => [
                'from' => Carbon::now()->startOfMonth()->startOfDay()->format('Y-m-d'),
                'to' => Carbon::now()->endOfMonth()->endOfDay()->format('Y-m-d'),
            ]
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/orders/dashboard?'. $data)
            ->assertValid();

        // test with invalid fix range value
        $data = http_build_query([
            'fixRange' => 'dummy-fix-range'
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/orders/dashboard?'. $data)
            ->assertJsonValidationErrors([
                'fixRange'
            ]);

        // test with correct fix range
        $data = http_build_query([
            'page' => 1,
            'limit' => 15,
            'desc' => 1,
            'fixRange' => 'monthly'
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/orders/dashboard?'. $data)
            ->assertValid();
    }

    public function test_order_dashboard_with_fix_range()
    {
        list('user' => $user, 'token' => $token) = $this->setupAdminUserAndToken();

        // run the seeder for the order statuses
        $this->seed();

        $paidOrders = $this->createPaidOrders(
            startDate: Carbon::now()->startOfDay(),
            endDate: Carbon::now()->endOfDay(),
            count: 10,
        );

        $unpaidOrders = $this->createUnpaidOrders(
            startDate: Carbon::now()->startOfDay(),
            endDate: Carbon::now()->endOfDay(),
            count: 10,
        );

        $paramsData = http_build_query([
            'fixRange' => 'today',
            'page' => 1,
            'limit' => 15,
            'sortBy' => 'status',
            'desc' => 0
        ]);

        $response = $this->withToken($token)
            ->getJson('/api/v1/orders/dashboard?'. $paramsData)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'total_earnings',
                    'potential_earnings',
                    'total_orders',
                    'chart_data',
                    'orders',
                ]
            ]);

        $responseData = $response->json();

        $this->assertEquals($responseData['data']['total_earnings'], format_number($paidOrders->sum('amount')));
        $this->assertEquals($responseData['data']['potential_earnings'], format_number($unpaidOrders->sum('amount')));
        $this->assertEquals($responseData['data']['total_orders'], $paidOrders->count() + $unpaidOrders->count());
    }

    public function test_order_dashboard_with_date_range()
    {
        list('user' => $user, 'token' => $token) = $this->setupAdminUserAndToken();

        // run the seeder for the order statuses
        $this->seed();

        $paidOrders = $this->createPaidOrders(
            startDate: Carbon::now()->startOfDay(),
            endDate: Carbon::now()->endOfDay(),
            count: 10,
        );

        $unpaidOrders = $this->createUnpaidOrders(
            startDate: Carbon::now()->startOfDay(),
            endDate: Carbon::now()->endOfDay(),
            count: 10,
        );

        $paramsData = http_build_query([
            'page' => 1,
            'limit' => 15,
            'desc' => 1,
            'dateRange' => [
                'from' => Carbon::now()->startOfDay()->format('Y-m-d'),
                'to' => Carbon::now()->endOfDay()->format('Y-m-d'),
            ]
        ]);

        $response = $this->withToken($token)
            ->getJson('/api/v1/orders/dashboard?'. $paramsData)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'total_earnings',
                    'potential_earnings',
                    'total_orders',
                    'chart_data',
                    'orders',
                ]
            ]);

        $responseData = $response->json();

        $this->assertEquals($responseData['data']['total_earnings'], format_number($paidOrders->sum('amount')));
        $this->assertEquals($responseData['data']['potential_earnings'], format_number($unpaidOrders->sum('amount')));
        $this->assertEquals($responseData['data']['total_orders'], $paidOrders->count() + $unpaidOrders->count());
    }
}
