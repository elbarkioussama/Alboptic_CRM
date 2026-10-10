<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;

class OrderCalculationService
{
    /**
     * Calculate financials for a single order item.
     *
     * @return array{subtotal: float, total: float}
     */
    public function calculateItemTotals(int $quantity, float $unitPrice, float $discount = 0.0, float $taxRate = 20.0): array
    {
        $quantity = max(1, $quantity);
        $unitPrice = round(max(0.0, $unitPrice), 2);
        $discount = round(max(0.0, $discount), 2);
        $taxRate = max(0.0, $taxRate);

        $gross = round($quantity * $unitPrice, 2);
        $subtotal = round(max(0.0, $gross - $discount), 2);
        $taxAmount = round($subtotal * ($taxRate / 100.0), 2);
        $total = round($subtotal + $taxAmount, 2);

        return [
            'subtotal' => $subtotal,
            'total' => $total,
        ];
    }

    /**
     * Determine payment status based on total TTC and paid amount.
     */
    public function determinePaymentStatus(float $totalAmount, float $amountPaid): PaymentStatus
    {
        $totalAmount = round($totalAmount, 2);
        $amountPaid = round($amountPaid, 2);

        if ($amountPaid <= 0.0) {
            return PaymentStatus::Unpaid;
        }

        if ($amountPaid >= $totalAmount && $totalAmount > 0.0) {
            return PaymentStatus::Paid;
        }

        return PaymentStatus::PartiallyPaid;
    }

    /**
     * Recalculate order totals from its order items and payments.
     */
    public function recalculateOrder(Order $order): Order
    {
        $order->load(['items', 'payments']);

        $subtotal = 0.0;
        foreach ($order->items as $item) {
            $subtotal += (float) $item->subtotal;
        }
        $subtotal = round($subtotal, 2);

        $orderDiscount = round((float) $order->discount, 2);
        $taxableAmount = round(max(0.0, $subtotal - $orderDiscount), 2);
        $taxRate = (float) ($order->tax_rate ?? 20.0);
        $taxAmount = round($taxableAmount * ($taxRate / 100.0), 2);
        $totalAmount = round($taxableAmount + $taxAmount, 2);

        $amountPaid = round((float) $order->payments->sum('amount'), 2);
        $remainingAmount = round(max(0.0, $totalAmount - $amountPaid), 2);
        $paymentStatus = $this->determinePaymentStatus($totalAmount, $amountPaid);

        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'remaining_amount' => $remainingAmount,
            'payment_status' => $paymentStatus,
        ]);

        return $order->fresh(['items', 'payments']);
    }
}
