<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\Order;
use App\Models\StoreSetting;
use App\Services\DocumentNumberGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentNumberGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    private DocumentNumberGeneratorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        StoreSetting::current();
        $this->service = new DocumentNumberGeneratorService();
    }

    public function test_it_generates_first_order_number_with_correct_format(): void
    {
        $year = date('Y');
        $number = $this->service->generateOrderNumber();

        $this->assertEquals("BC-{$year}-000001", $number);
    }

    public function test_it_increments_order_numbers_sequentially(): void
    {
        $year = date('Y');

        Order::factory()->create([
            'order_number' => "BC-{$year}-000005",
        ]);

        $nextNumber = $this->service->generateOrderNumber();
        $this->assertEquals("BC-{$year}-000006", $nextNumber);
    }

    public function test_it_generates_facture_and_bon_commande_document_numbers(): void
    {
        $year = date('Y');

        $bcNumber = $this->service->generateDocumentNumber(DocumentType::BonCommande);
        $this->assertEquals("BC-{$year}-000001", $bcNumber);

        $facNumber = $this->service->generateDocumentNumber(DocumentType::Facture);
        $this->assertEquals("FAC-{$year}-000001", $facNumber);

        Document::factory()->create([
            'document_type' => DocumentType::Facture,
            'document_number' => "FAC-{$year}-000042",
        ]);

        $nextFac = $this->service->generateDocumentNumber(DocumentType::Facture);
        $this->assertEquals("FAC-{$year}-000043", $nextFac);
    }
}
