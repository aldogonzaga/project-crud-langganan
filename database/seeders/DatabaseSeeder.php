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
                'currency' => '$',
                'password' => Hash::make('password123'),
            ]
        );

        // Clear existing demo subscriptions before re-seeding
        Subscription::where('user_id', $demoUser->id)->forceDelete();

        $today = Carbon::today();

        $demoSubscriptions = [
            [
                'name' => 'Spotify Premium',
                'cost' => 10.99,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(2)->toDateString(), // 2 days away -> RED alert!
                'category' => 'Entertainment',
                'status' => 'Active',
                'icon' => '🎵',
                'notes' => 'Individual plan with offline music downloads and zero ads.',
            ],
            [
                'name' => 'Netflix Standard',
                'cost' => 15.49,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(5)->toDateString(), // 5 days away -> Yellow alert!
                'category' => 'Entertainment',
                'status' => 'Active',
                'icon' => '🎬',
                'notes' => 'HD streaming on 2 devices simultaneously.',
            ],
            [
                'name' => 'Amazon Prime',
                'cost' => 14.99,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(19)->toDateString(),
                'category' => 'Shopping',
                'status' => 'Active',
                'icon' => '📦',
                'notes' => 'Free 2-day delivery + Prime Video streaming.',
            ],
            [
                'name' => 'Adobe Creative Cloud',
                'cost' => 54.99,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(12)->toDateString(),
                'category' => 'Work',
                'status' => 'Active',
                'icon' => '🎨',
                'notes' => 'Full suite including Photoshop, Illustrator, and Premiere Pro.',
            ],
            [
                'name' => 'GitHub Copilot',
                'cost' => 10.00,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(9)->toDateString(),
                'category' => 'Work',
                'status' => 'Active',
                'icon' => '💻',
                'notes' => 'AI pair programmer for VS Code and terminal.',
            ],
            [
                'name' => 'Equinox / Local Gym',
                'cost' => 45.00,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(15)->toDateString(),
                'category' => 'Health',
                'status' => 'Active',
                'icon' => '🏋️',
                'notes' => 'Full gym access, swimming pool and weekend classes.',
            ],
            [
                'name' => 'iCloud+ 2TB',
                'cost' => 9.99,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(24)->toDateString(),
                'category' => 'Utilities',
                'status' => 'Active',
                'icon' => '☁️',
                'notes' => 'Cloud backup for iPhone, iPad, and MacBook + Private Relay.',
            ],
            [
                'name' => 'Fresh Meal Box Delivery',
                'cost' => 32.50,
                'billing_cycle' => 'Weekly',
                'next_billing_date' => $today->copy()->addDays(3)->toDateString(), // 3 days away -> RED alert!
                'category' => 'Food',
                'status' => 'Active',
                'icon' => '🥗',
                'notes' => '3 meals per week organic ingredients and recipes.',
            ],
            [
                'name' => 'JetBrains All Products Pack',
                'cost' => 289.00,
                'billing_cycle' => 'Yearly',
                'next_billing_date' => $today->copy()->addDays(140)->toDateString(),
                'category' => 'Work',
                'status' => 'Active',
                'icon' => '⚡',
                'notes' => 'Annual developer license including PhpStorm, WebStorm, and DataGrip.',
            ],
            [
                'name' => 'Figma Professional',
                'cost' => 15.00,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(2)->toDateString(), // 2 days away -> Critical trial alert!
                'category' => 'Work',
                'status' => 'Free Trial',
                'icon' => '📐',
                'notes' => '14-day trial of team collaboration and dev mode. Cancel before renewal!',
            ],
            [
                'name' => 'Coursera Plus',
                'cost' => 59.00,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(6)->toDateString(),
                'category' => 'Education',
                'status' => 'Free Trial',
                'icon' => '📚',
                'notes' => '7-day trial for AI Specialization courses.',
            ],
            [
                'name' => 'Audible Premium Plus',
                'cost' => 14.95,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->addDays(11)->toDateString(),
                'category' => 'Entertainment',
                'status' => 'Cancelled',
                'icon' => '🎧',
                'notes' => 'Cancelled last month to cut costs. Saved $14.95/mo.',
            ],
            [
                'name' => 'Old MMO Game Pass',
                'cost' => 12.99,
                'billing_cycle' => 'Monthly',
                'next_billing_date' => $today->copy()->subDays(5)->toDateString(),
                'category' => 'Entertainment',
                'status' => 'Cancelled',
                'icon' => '🎮',
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
                'currency' => '$',
                'password' => Hash::make('password'),
            ]
        );
    }
}
