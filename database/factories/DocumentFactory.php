<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        $type = fake()->randomElement(DocumentType::cases());
        $prefix = $type === DocumentType::Facture ? 'FAC-' : 'BC-';

        return [
            'order_id' => Order::factory(),
            'document_type' => $type,
            'document_number' => $prefix . date('Y') . '-' . fake()->unique()->numerify('######'),
            'file_path' => null,
            'generated_at' => now(),
        ];
    }
}
