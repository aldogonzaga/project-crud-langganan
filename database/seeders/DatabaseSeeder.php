<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create or update Demo User
        $demoUser = User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Alex Morgan',
                'currency' => 'Rp',
                'password' => Hash::make('password123'),
            ]
        );

        // Clear existing demo subscriptions before re-seeding
        Subscription::where('user_id', $demoUser->id)->forceDelete();

        $today = Carbon::today();

        $demoSubscriptions = [
            [
                'name' => 'Spotify Premium',
                'cost' => 54990,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(2)->toDateString(), // 2 days away -> RED alert!
                'category' => 'Entertainment',
                'status' => 'Active',
                'icon' => 'bi-spotify',
                'notes' => 'Individual plan with offline music downloads and zero ads.',
            ],
            [
                'name' => 'Netflix Standard',
                'cost' => 186000,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(5)->toDateString(), // 5 days away -> Yellow alert!
                'category' => 'Entertainment',
                'status' => 'Active',
                'icon' => 'bi-film',
                'notes' => 'HD streaming on 2 devices simultaneously.',
            ],
            [
                'name' => 'Amazon Prime',
                'cost' => 59000,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(19)->toDateString(),
                'category' => 'Shopping',
                'status' => 'Active',
                'icon' => 'bi-box-seam',
                'notes' => 'Free 2-day delivery + Prime Video streaming.',
            ],
            [
                'name' => 'Adobe Creative Cloud',
                'cost' => 715000,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(12)->toDateString(),
                'category' => 'Work',
                'status' => 'Active',
                'icon' => 'bi-palette',
                'notes' => 'Full suite including Photoshop, Illustrator, and Premiere Pro.',
            ],
            [
                'name' => 'GitHub Copilot',
                'cost' => 150000,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(9)->toDateString(),
                'category' => 'Work',
                'status' => 'Active',
                'icon' => 'bi-github',
                'notes' => 'AI pair programmer for VS Code and terminal.',
            ],
            [
                'name' => 'Equinox / Local Gym',
                'cost' => 350000,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(15)->toDateString(),
                'category' => 'Health',
                'status' => 'Active',
                'icon' => 'bi-heart-pulse',
                'notes' => 'Full gym access, swimming pool and weekend classes.',
            ],
            [
                'name' => 'iCloud+ 2TB',
                'cost' => 149000,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(24)->toDateString(),
                'category' => 'Utilities',
                'status' => 'Active',
                'icon' => 'bi-cloud',
                'notes' => 'Cloud backup for iPhone, iPad, and MacBook + Private Relay.',
            ],
            [
                'name' => 'Fresh Meal Box Delivery',
                'cost' => 250000,
                'billing_cycle' => 'Weekly',
                'next_billing_date' => $today->copy()->addDays(3)->toDateString(), // 3 days away -> RED alert!
                'category' => 'Food',
                'status' => 'Active',
                'icon' => 'bi-cup-hot',
                'notes' => '3 meals per week organic ingredients and recipes.',
            ],
            [
                'name' => 'JetBrains All Products Pack',
                'cost' => 3800000,
                'billing_cycle' => 'Yearly',
                'next_billing_date' => $today->copy()->addDays(140)->toDateString(),
                'category' => 'Work',
                'status' => 'Active',
                'icon' => 'bi-lightning-charge',
                'notes' => 'Annual developer license including PhpStorm, WebStorm, and DataGrip.',
            ],
            [
                'name' => 'Figma Professional',
                'cost' => 180000,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(2)->toDateString(), // 2 days away -> Critical trial alert!
                'category' => 'Work',
                'status' => 'Free Trial',
                'icon' => 'bi-figma',
                'notes' => '14-day trial of team collaboration and dev mode. Cancel before renewal!',
            ],
            [
                'name' => 'Coursera Plus',
                'cost' => 600000,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(6)->toDateString(),
                'category' => 'Education',
                'status' => 'Free Trial',
                'icon' => 'bi-book',
                'notes' => '7-day trial for AI Specialization courses.',
            ],
            [
                'name' => 'Audible Premium Plus',
                'cost' => 150000,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(11)->toDateString(),
                'category' => 'Entertainment',
                'status' => 'Cancelled',
                'icon' => 'bi-headphones',
                'notes' => 'Cancelled last month to cut costs. Saved Rp 150.000/mo.',
            ],
            [
                'name' => 'Old MMO Game Pass',
                'cost' => 130000,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->subDays(5)->toDateString(),
                'category' => 'Entertainment',
                'status' => 'Cancelled',
                'icon' => 'bi-controller',
                'notes' => 'Cancelled account.',
            ],
        ];

        foreach ($demoSubscriptions as $subData) {
            $demoUser->subscriptions()->create($subData);
        }

        // 2. Also ensure Test User exists
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'currency' => 'Rp',
                'password' => Hash::make('password'),
            ]
        );
    }
}