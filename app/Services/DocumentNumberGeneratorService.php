<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\Order;
use App\Models\StoreSetting;

class DocumentNumberGeneratorService
{
    /**
     * Generate sequential Order number (e.g. BC-2026-000001)
     */
    public function generateOrderNumber(): string
    {
        $year = date('Y');
        $setting = StoreSetting::current();
        $prefix = $setting->order_prefix ?: 'BC-';

        $latest = Order::withTrashed()
            ->where('order_number', 'like', "{$prefix}{$year}-%")
            ->orderByDesc('id')
            ->value('order_number');

        $sequence = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return sprintf('%s%s-%06d', $prefix, $year, $sequence);
    }

    /**
     * Generate sequential Document number (e.g. FAC-2026-000001)
     */
    public function generateDocumentNumber(DocumentType $type): string
    {
        $year = date('Y');
        $setting = StoreSetting::current();
        $prefix = $type === DocumentType::Facture
            ? ($setting->invoice_prefix ?: 'FAC-')
            : ($setting->order_prefix ?: 'BC-');

        $latest = Document::withTrashed()
            ->where('document_type', $type->value)
            ->where('document_number', 'like', "{$prefix}{$year}-%")
            ->orderByDesc('id')
            ->value('document_number');

        $sequence = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return sprintf('%s%s-%06d', $prefix, $year, $sequence);
    }
}
