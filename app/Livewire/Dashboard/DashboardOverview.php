<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Enums\OrderStatus;
use App\Enums\ReminderStatus;
use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Reminder;
use App\Models\StoreSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Tableau de bord — ALBOPTIC CRM')]
class DashboardOverview extends Component
{
    public function render()
    {
        $today = now()->toDateString();
        $storeSetting = StoreSetting::current();

        // 1. Core KPIs
        $todaySales = (float) Order::whereDate('order_date', $today)
            ->whereNotIn('status', [OrderStatus::Cancelled->value])
            ->sum('total_amount');

        $todayPayments = (float) Payment::whereDate('payment_date', $today)
            ->sum('amount');

        $outstandingPayments = (float) Order::whereNotIn('status', [OrderStatus::Cancelled->value])
            ->where('remaining_amount', '>', 0)
            ->sum('remaining_amount');

        $todayOrdersCount = Order::whereDate('order_date', $today)->count();
        $totalClientsCount = Client::count();

        // 2. Data Widgets
        $recentOrders = Order::with(['client', 'payments'])
            ->latest('order_date')
            ->latest('id')
            ->take(5)
            ->get();

        $upcomingReminders = Reminder::with('client')
            ->where('status', ReminderStatus::Pending->value)
            ->orderBy('reminder_date', 'asc')
            ->take(5)
            ->get();

        $outstandingOrders = Order::with('client')
            ->whereNotIn('status', [OrderStatus::Cancelled->value])
            ->where('remaining_amount', '>', 0)
            ->orderByDesc('remaining_amount')
            ->take(5)
            ->get();

        return view('livewire.dashboard.dashboard-overview', [
            'storeSetting' => $storeSetting,
            'todaySales' => $todaySales,
            'todayPayments' => $todayPayments,
            'outstandingPayments' => $outstandingPayments,
            'todayOrdersCount' => $todayOrdersCount,
            'totalClientsCount' => $totalClientsCount,
            'recentOrders' => $recentOrders,
            'upcomingReminders' => $upcomingReminders,
            'outstandingOrders' => $outstandingOrders,
        ]);
    }
}
