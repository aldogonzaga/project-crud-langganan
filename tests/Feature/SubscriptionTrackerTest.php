<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class SubscriptionTrackerTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_register_and_is_redirected_to_dashboard(): void
    {
        $email = 'newuser_' . time() . '@example.com';
        $response = $this->post(route('register.post'), [
            'name' => 'John Doe',
            'email' => $email,
            'currency' => '$',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['email' => $email]);
    }

    public function test_user_can_login_with_demo_link(): void
    {
        $response = $this->get(route('demo.login'));
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_can_create_subscription(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('subscriptions.store'), [
            'name' => 'Spotify Premium',
            'cost' => 10.99,
            'billing_cycle' => 'Monthly',
            'next_billing_date' => Carbon::today()->addDays(15)->toDateString(),
            'category' => 'Entertainment',
            'status' => 'Active',
            'icon' => '🎵',
            'notes' => 'Music streaming plan',
        ]);

        $response->assertRedirect(route('subscriptions.index'));
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'name' => 'Spotify Premium',
            'cost' => 10.99,
        ]);
    }

    public function test_user_can_update_subscription_price_and_status(): void
    {
        $user = User::factory()->create();
        $sub = Subscription::create([
            'user_id' => $user->id,
            'name' => 'Netflix',
            'cost' => 9.99,
            'billing_cycle' => 'Monthly',
            'next_billing_date' => Carbon::today()->addDays(10)->toDateString(),
            'category' => 'Entertainment',
            'status' => 'Active',
        ]);

        // Rate hike update & change status to Cancelled
        $response = $this->actingAs($user)->put(route('subscriptions.update', $sub), [
            'name' => 'Netflix Standard',
            'cost' => 15.49,
            'billing_cycle' => 'Monthly',
            'next_billing_date' => Carbon::today()->addDays(10)->toDateString(),
            'category' => 'Entertainment',
            'status' => 'Cancelled',
            'icon' => '🎬',
            'notes' => 'Cancelled after price hike',
        ]);

        $response->assertRedirect(route('subscriptions.index'));
        $sub->refresh();
        $this->assertEquals(15.49, (float) $sub->cost);
        $this->assertEquals('Cancelled', $sub->status);
    }

    public function test_cancelled_subscription_stops_adding_to_active_monthly_spend(): void
    {
        $user = User::factory()->create();

        // Active subscription
        Subscription::create([
            'user_id' => $user->id,
            'name' => 'Spotify',
            'cost' => 10.00,
            'billing_cycle' => 'Monthly',
            'next_billing_date' => Carbon::today()->addDays(10)->toDateString(),
            'category' => 'Entertainment',
            'status' => 'Active',
        ]);

        // Cancelled subscription
        Subscription::create([
            'user_id' => $user->id,
            'name' => 'Old Gym',
            'cost' => 50.00,
            'billing_cycle' => 'Monthly',
            'next_billing_date' => Carbon::today()->addDays(10)->toDateString(),
            'category' => 'Health',
            'status' => 'Cancelled',
        ]);

        $activeMonthly = $user->subscriptions()->active()->get()->sum(fn ($s) => $s->monthly_cost);
        $this->assertEquals(10.00, $activeMonthly);
    }

    public function test_date_logic_critical_urgency_for_bills_within_3_days(): void
    {
        $user = User::factory()->create();

        // Sub due in 2 days -> must be 'critical'
        $urgentSub = Subscription::create([
            'user_id' => $user->id,
            'name' => 'Urgent Bill',
            'cost' => 20.00,
            'billing_cycle' => 'Monthly',
            'next_billing_date' => Carbon::today()->addDays(2)->toDateString(),
            'category' => 'Utilities',
            'status' => 'Active',
        ]);

        // Sub due in 5 days -> must be 'warning'
        $warningSub = Subscription::create([
            'user_id' => $user->id,
            'name' => 'Warning Bill',
            'cost' => 25.00,
            'billing_cycle' => 'Monthly',
            'next_billing_date' => Carbon::today()->addDays(5)->toDateString(),
            'category' => 'Utilities',
            'status' => 'Active',
        ]);

        // Sub due in 20 days -> must be 'normal'
        $normalSub = Subscription::create([
            'user_id' => $user->id,
            'name' => 'Future Bill',
            'cost' => 30.00,
            'billing_cycle' => 'Monthly',
            'next_billing_date' => Carbon::today()->addDays(20)->toDateString(),
            'category' => 'Utilities',
            'status' => 'Active',
        ]);

        $this->assertEquals('critical', $urgentSub->urgency_level);
        $this->assertEquals('warning', $warningSub->urgency_level);
        $this->assertEquals('normal', $normalSub->urgency_level);
    }

    public function test_soft_delete_and_restore(): void
    {
        $user = User::factory()->create();
        $sub = Subscription::create([
            'user_id' => $user->id,
            'name' => 'Mistake Sub',
            'cost' => 5.00,
            'billing_cycle' => 'Monthly',
            'next_billing_date' => Carbon::today()->addDays(10)->toDateString(),
            'category' => 'Others',
            'status' => 'Active',
        ]);

        // Delete (Soft Delete)
        $response = $this->actingAs($user)->delete(route('subscriptions.destroy', $sub));
        $response->assertRedirect(route('subscriptions.index'));

        $this->assertSoftDeleted('subscriptions', ['id' => $sub->id]);

        // Restore
        $restoreResponse = $this->actingAs($user)->post(route('subscriptions.restore', $sub->id));
        $restoreResponse->assertRedirect(route('subscriptions.trash'));

        $this->assertNotSoftDeleted('subscriptions', ['id' => $sub->id]);
    }

    public function test_dashboard_page_loads_with_metrics_and_charts(): void
    {
        $user = User::where('email', 'demo@example.com')->first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Total Monthly Spend');
        $response->assertSee('Payments Due in Next 7 Days');
        $response->assertSee('Spending by Category');
    }

    public function test_analytics_page_loads_with_visualizations(): void
    {
        $user = User::where('email', 'demo@example.com')->first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $response = $this->actingAs($user)->get(route('analytics.index'));
        $response->assertStatus(200);
        $response->assertSee('Monthly Spending by Category');
        $response->assertSee('Billing Cycle Breakdown');
        $response->assertSee('Top 5 Most Expensive Subscriptions');
    }

    public function test_free_trials_watchdog_page(): void
    {
        $user = User::where('email', 'demo@example.com')->first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $response = $this->actingAs($user)->get(route('trials.index'));
        $response->assertStatus(200);
        $response->assertSee('Free Trials Watchdog');
    }
}

