<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\DocumentType;
use App\Enums\ItemType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ProductCategory;
use App\Enums\ReminderStatus;
use App\Enums\ReminderType;
use App\Models\Client;
use App\Models\Document;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\Reminder;
use App\Models\User;
use App\Services\DocumentNumberGeneratorService;
use App\Services\OrderCalculationService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $numGen = new DocumentNumberGeneratorService();
        $calcService = new OrderCalculationService();

        // 1. Seed Real Optical Products
        $products = [
            [
                'sku' => 'RB-5154-2000',
                'name' => 'Ray-Ban Clubmaster Optics 5154',
                'category' => ProductCategory::Frame,
                'brand' => 'Ray-Ban',
                'supplier' => 'Luxottica Maroc',
                'cost_price' => 550.00,
                'selling_price' => 1250.00,
                'stock_quantity' => 8,
            ],
            [
                'sku' => 'TF-5295-001',
                'name' => 'Tom Ford Square Frame 5295',
                'category' => ProductCategory::Frame,
                'brand' => 'Tom Ford',
                'supplier' => 'Marcolin Distribution',
                'cost_price' => 1100.00,
                'selling_price' => 2400.00,
                'stock_quantity' => 4,
            ],
            [
                'sku' => 'GC-0061O-002',
                'name' => 'Gucci Havana Oval Frame',
                'category' => ProductCategory::Frame,
                'brand' => 'Gucci',
                'supplier' => 'Kering Eyewear',
                'cost_price' => 1350.00,
                'selling_price' => 2900.00,
                'stock_quantity' => 3,
            ],
            [
                'sku' => 'ESS-CRIZAL-160',
                'name' => 'Essilor Crizal Prevencia 1.60 (Verre unifocal)',
                'category' => ProductCategory::Lens,
                'brand' => 'Essilor',
                'supplier' => 'Essilor Sivo Maroc',
                'cost_price' => 220.00,
                'selling_price' => 550.00,
                'stock_quantity' => 40,
            ],
            [
                'sku' => 'HOYA-PROG-167',
                'name' => 'Hoya Hoyalux iD LifeStyle 3 1.67 (Progressif)',
                'category' => ProductCategory::Lens,
                'brand' => 'Hoya',
                'supplier' => 'Hoya Maroc',
                'cost_price' => 750.00,
                'selling_price' => 1800.00,
                'stock_quantity' => 20,
            ],
            [
                'sku' => 'RB-2140-901',
                'name' => 'Ray-Ban Wayfarer Classic Polarized',
                'category' => ProductCategory::Sunglasses,
                'brand' => 'Ray-Ban',
                'supplier' => 'Luxottica Maroc',
                'cost_price' => 700.00,
                'selling_price' => 1650.00,
                'stock_quantity' => 6,
            ],
            [
                'sku' => 'ACC-SPRAY-30ML',
                'name' => 'Kit Nettoyant Optique Spray 30ml + Microfibre',
                'category' => ProductCategory::Accessory,
                'brand' => 'Alboptic Care',
                'supplier' => 'OptiSupply Marrakech',
                'cost_price' => 15.00,
                'selling_price' => 45.00,
                'stock_quantity' => 100,
            ],
            [
                'sku' => 'SRV-MONTAGE',
                'name' => 'Montage et Meulage Spécial en Atelier',
                'category' => ProductCategory::Service,
                'brand' => null,
                'supplier' => null,
                'cost_price' => 0.00,
                'selling_price' => 100.00,
                'stock_quantity' => 999,
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['sku' => $p['sku']], $p);
        }

        // 2. Seed Real Clients & Workflow
        $clientsData = [
            [
                'first_name' => 'Amine',
                'last_name' => 'El Mansouri',
                'phone' => '+212 6 61 23 45 67',
                'email' => 'amine.mansouri@example.ma',
                'gender' => 'male',
                'date_of_birth' => '1988-04-12',
                'address' => 'Hivernage, Résidence Les Palmiers Apt 4',
                'city' => 'Marrakech',
                'notes' => 'Client fidèle. Travaille sur écran, demande anti-lumière bleue.',
            ],
            [
                'first_name' => 'Khadija',
                'last_name' => 'Benjelloun',
                'phone' => '+212 6 62 98 76 54',
                'email' => 'k.benjelloun@example.ma',
                'gender' => 'female',
                'date_of_birth' => '1974-09-25',
                'address' => 'Targa, Villa 12 Allée des Roses',
                'city' => 'Marrakech',
                'notes' => 'Presbyte, porte des verres progressifs.',
            ],
            [
                'first_name' => 'Yassine',
                'last_name' => 'Berrada',
                'phone' => '+212 6 70 11 22 33',
                'email' => 'yassine.b@example.ma',
                'gender' => 'male',
                'date_of_birth' => '1995-11-03',
                'address' => 'Daoudiate, Rue Al Badii',
                'city' => 'Marrakech',
                'notes' => 'Myope, amateur de montures fines.',
            ],
        ];

        $createdClients = [];
        foreach ($clientsData as $c) {
            $client = Client::updateOrCreate(['phone' => $c['phone']], $c);
            $createdClients[] = $client;
        }

        // 3. Create Prescriptions for Clients
        // Prescription 1 for Amine (Single vision + Astigmatism)
        $p1 = Prescription::create([
            'client_id' => $createdClients[0]->id,
            'prescription_date' => now()->subMonths(2)->toDateString(),
            'prescribed_by' => 'Dr. Alaoui (Ophtalmologue Guéliz)',
            'od_sphere' => -2.25,
            'od_cylinder' => -0.75,
            'od_axis' => 85,
            'od_addition' => null,
            'og_sphere' => -2.00,
            'og_cylinder' => -0.50,
            'og_axis' => 95,
            'og_addition' => null,
            'pd' => 64.0,
            'pd_right' => 32.0,
            'pd_left' => 32.0,
            'notes' => 'Anti-reflet haute performance recommandé.',
        ]);

        // Prescription 2 for Khadija (Progressive)
        $p2 = Prescription::create([
            'client_id' => $createdClients[1]->id,
            'prescription_date' => now()->subDays(15)->toDateString(),
            'prescribed_by' => 'Dr. Tazi (Clinique de la Vision)',
            'od_sphere' => 1.50,
            'od_cylinder' => -0.50,
            'od_axis' => 180,
            'od_addition' => 2.00,
            'og_sphere' => 1.75,
            'og_cylinder' => -0.75,
            'og_axis' => 170,
            'og_addition' => 2.00,
            'pd' => 62.0,
            'pd_right' => 31.0,
            'pd_left' => 31.0,
            'notes' => 'Verres progressifs couloir court ou standard.',
        ]);

        // 4. Create Order 1 (Amine - Delivered, Paid)
        $order1 = Order::create([
            'client_id' => $createdClients[0]->id,
            'prescription_id' => $p1->id,
            'order_number' => $numGen->generateOrderNumber(),
            'order_date' => now()->subMonths(2)->toDateString(),
            'status' => OrderStatus::Delivered,
            'subtotal' => 2350.00,
            'discount' => 150.00,
            'tax_rate' => 20.00,
            'tax_amount' => 440.00,
            'total_amount' => 2640.00,
            'amount_paid' => 2640.00,
            'remaining_amount' => 0.00,
            'payment_status' => PaymentStatus::Paid,
            'notes' => 'Commande livrée et ajustée avec succès.',
            'created_by' => $admin?->id,
        ]);

        $frame1 = Product::where('sku', 'RB-5154-2000')->first();
        $lens1 = Product::where('sku', 'ESS-CRIZAL-160')->first();

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $frame1?->id,
            'item_type' => ItemType::Frame,
            'description' => 'Monture Ray-Ban Clubmaster Optics 5154',
            'quantity' => 1,
            'unit_price' => 1250.00,
            'discount' => 0.00,
            'tax_rate' => 20.00,
            'subtotal' => 1250.00,
            'total' => 1500.00,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $lens1?->id,
            'item_type' => ItemType::Lens,
            'description' => 'Paire Essilor Crizal Prevencia 1.60 (OD: -2.25(-0.75)85° / OG: -2.00(-0.50)95°)',
            'quantity' => 2,
            'unit_price' => 550.00,
            'discount' => 0.00,
            'tax_rate' => 20.00,
            'subtotal' => 1100.00,
            'total' => 1320.00,
        ]);

        Payment::create([
            'order_id' => $order1->id,
            'amount' => 1000.00,
            'payment_date' => now()->subMonths(2)->toDateString(),
            'payment_method' => PaymentMethod::Card,
            'reference' => 'TPE-92817',
            'notes' => 'Avance à la commande',
            'created_by' => $admin?->id,
        ]);

        Payment::create([
            'order_id' => $order1->id,
            'amount' => 1640.00,
            'payment_date' => now()->subMonths(2)->addDays(4)->toDateString(),
            'payment_method' => PaymentMethod::Cash,
            'reference' => 'RECU-001',
            'notes' => 'Solde à la livraison',
            'created_by' => $admin?->id,
        ]);

        Document::create([
            'order_id' => $order1->id,
            'document_type' => DocumentType::Facture,
            'document_number' => $numGen->generateDocumentNumber(DocumentType::Facture),
            'generated_at' => now()->subMonths(2)->addDays(4),
        ]);

        // 5. Create Order 2 (Khadija - Ready, Partially Paid)
        $order2 = Order::create([
            'client_id' => $createdClients[1]->id,
            'prescription_id' => $p2->id,
            'order_number' => $numGen->generateOrderNumber(),
            'order_date' => now()->subDays(3)->toDateString(),
            'status' => OrderStatus::Ready,
            'subtotal' => 6000.00,
            'discount' => 500.00,
            'tax_rate' => 20.00,
            'tax_amount' => 1100.00,
            'total_amount' => 6600.00,
            'amount_paid' => 2500.00,
            'remaining_amount' => 4100.00,
            'payment_status' => PaymentStatus::PartiallyPaid,
            'notes' => 'Lunettes prêtes en atelier. Relancer pour retrait.',
            'created_by' => $admin?->id,
        ]);

        $frame2 = Product::where('sku', 'TF-5295-001')->first();
        $lens2 = Product::where('sku', 'HOYA-PROG-167')->first();

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $frame2?->id,
            'item_type' => ItemType::Frame,
            'description' => 'Monture Tom Ford 5295 Acétate Noir',
            'quantity' => 1,
            'unit_price' => 2400.00,
            'discount' => 0.00,
            'tax_rate' => 20.00,
            'subtotal' => 2400.00,
            'total' => 2880.00,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $lens2?->id,
            'item_type' => ItemType::Lens,
            'description' => 'Paire Hoya Progressive LifeStyle 3 1.67',
            'quantity' => 2,
            'unit_price' => 1800.00,
            'discount' => 0.00,
            'tax_rate' => 20.00,
            'subtotal' => 3600.00,
            'total' => 4320.00,
        ]);

        Payment::create([
            'order_id' => $order2->id,
            'amount' => 2500.00,
            'payment_date' => now()->subDays(3)->toDateString(),
            'payment_method' => PaymentMethod::Card,
            'reference' => 'TPE-93402',
            'notes' => 'Avance initiale',
            'created_by' => $admin?->id,
        ]);

        Document::create([
            'order_id' => $order2->id,
            'document_type' => DocumentType::BonCommande,
            'document_number' => $numGen->generateDocumentNumber(DocumentType::BonCommande),
            'generated_at' => now()->subDays(3),
        ]);

        // Reminders for Order 2
        Reminder::create([
            'client_id' => $createdClients[1]->id,
            'order_id' => $order2->id,
            'reminder_type' => ReminderType::Pickup,
            'reminder_date' => now()->toDateString(),
            'status' => ReminderStatus::Pending,
            'notes' => 'Appeler Mme Benjelloun : Lunettes prêtes au magasin.',
        ]);

        // Annual vision check reminder for Client 0
        Reminder::create([
            'client_id' => $createdClients[0]->id,
            'order_id' => null,
            'reminder_type' => ReminderType::PrescriptionCheck,
            'reminder_date' => now()->addMonths(10)->toDateString(),
            'status' => ReminderStatus::Pending,
            'notes' => 'Contrôle annuel de la vision pour M. El Mansouri.',
        ]);
    }
}
