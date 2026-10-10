---
name: alboptic_skills
description: Comprehensive domain specification, architectural guidelines, database schema, optical measurements standards, financial logic, and development rules for ALBOPTIC CRM — a specialized optician store management system (Clients, Prescriptions, Orders, Payments, Facturation, Bon de Commande, Reminders, and Analytics) built with Laravel & Livewire.
---
# ALBOPTIC CRM — Project Skill & Specification

## Project Identity

You are working on **ALBOPTIC CRM**, a tailored web application designed specifically for an optical/eyewear store in Marrakech, Morocco.

This is an **optician-specific business CRM**, not a generic CRM or a generic retail ERP.

The primary goal is to replace manual/Notion-based workflows with a fast, reliable, robust application for daily optical-store operations.

### Core Workflow:

```text
CLIENT
→ PRESCRIPTION (OD / OG / PD / Heights / Prisms)
→ ORDER (Frames, Lenses OD/OG, Treatments, Services)
→ ORDER ITEMS
→ PAYMENT & ADVANCE (Cash / Card / Transfer / etc.)
→ REMAINING BALANCE CALCULATION
→ BON DE COMMANDE / FACTURE (PDF generation with Moroccan legal standards)
→ REMINDERS (Pickups, Payments, Annual Checkups)
→ REAL-TIME ANALYTICS & DASHBOARD
```

The application must remain simple, responsive, and intuitive for daily counter operations by an optician while adhering strictly to high-standard software architecture for future evolution.

---

## Product Principles

Always prioritize:

1. **Correct business logic:** Optical domain nuances must be accurately represented.
2. **Data integrity & safety:** Zero tolerance for orphaned records, silent calculation errors, or lost history.
3. **Financial accuracy:** Server-side decimal calculations, strict balance validations, atomic database transactions.
4. **Fast daily workflow:** Creating an order with a prescription and down payment must take under 2 minutes.
5. **Simple, clean UX:** Tailored for quick counter use, touch-friendly, minimal clutter.
6. **Maintainable code:** Idiomatic Laravel, standard patterns, no unnecessary abstractions.
7. **Mobile/Tablet usability:** Fully functional on tablets and mobile screens.
8. **Security & Privacy:** Medical/prescription documents and client contact details must be protected.
9. **Extensibility without over-engineering:** Keep the MVP lean while designing clean database boundaries.

> [!IMPORTANT]
> Do not build a bloated ERP. Solve the real, practical optical-shop workflow first.

---

## Technology Stack

### Preferred Stack:

- **Framework:** Laravel 11+ / PHP 8.3+
- **Database:** PostgreSQL (or MySQL 8.0+ with strict mode)
- **Frontend / Reactivity:** Laravel Livewire 3 + Blade
- **Styling:** Tailwind CSS
- **Micro-interactions:** Alpine.js (only when needed for UI toggles/modals)
- **ORM:** Laravel Eloquent with Strict Mode enabled in development
- **Enums:** PHP 8.1+ Backed Enums
- **Authentication & Authorization:** Laravel built-in auth, Policies, and Gates
- **Testing:** Pest PHP or PHPUnit
- **PDF Generation:** Spatie Laravel-PDF (Chromium/Browsershot) or Dompdf (with proper UTF-8 and RTL/French support)
- **Charts:** Chart.js or ApexCharts via lightweight Blade/Livewire integration

### Architecture Rules:

- **NO** unnecessary client-heavy SPA frameworks (React, Next.js, Vue) unless requested.
- **NO** premature microservices, Docker orchestration overhead, Redis queues, or complex infrastructure for simple CRUD.
- **Server-rendered Laravel + Livewire** is the primary paradigm.
- All monetary and financial calculations **must be executed server-side** within dedicated service classes (`OrderCalculationService`, `PaymentService`).

---

## Business Domain & Optical Standards

### Optical Prescription Specifics:

Prescriptions follow international and Moroccan optometry standards:
- **OD (Œil Droit / Right Eye)** & **OG (Œil Gauche / Left Eye)** (also referenced as OS in international standards).
- **Sphère (Sphere / SPH):** Positive or negative values in increments of `0.25` (e.g., `-2.25`, `+1.50`).
- **Cylindre (Cylinder / CYL):** Astigmatism correction, in increments of `0.25` (typically negative or positive format).
- **Axe (Axis / AX):** Astigmatism angle, integer between `0°` and `180°`. Mandatory if Cylinder is present.
- **Addition (ADD):** Presbyopia near-vision power, positive value between `+0.75` and `+4.00` in `0.25` increments.
- **Écart Pupillaire (Pupillary Distance / PD):**
  - Total PD (e.g. `62 mm`) or Monocular PD: Right (`pd_right`, e.g. `31 mm`) and Left (`pd_left`, e.g. `31 mm`).
  - Far PD (Écart Loin) and Near PD (Écart Près).
- **Hauteur de montage (Fitting Height / SEG HT):** Essential for progressive lenses (`od_height`, `og_height` in mm).
- **Prisme & Base (Prism / Base):** Optional prism correction (`od_prism`, `od_base`, `og_prism`, `og_base`).

### Lens Classification & Attributes:

- **Lens Types:** Unifocal / Single Vision (Vision de loin / Vision de près), Progressive (Progressif), Bifocal, Dégressif / Office, Contact Lenses.
- **Lens Index:** `1.50` (Standard), `1.56` (Mid-index), `1.60` (Thin), `1.67` (Extra-thin), `1.74` (Ultra-thin), Polycarbonate (`1.59`).
- **Treatments & Coatings:**
  - Anti-reflet (Hard Multi-Coated / HMC)
  - Anti-lumière bleue (Blue Cut / Blue Shield)
  - Photochromique / Transition (Gris / Brun)
  - Polarisé (Polarized)
  - Teinté (Solar Tint: Cat 1, 2, 3)
  - Anti-rayures (Hard Coat)
  - Hydrophobe / Oléophobe

---

## Database Architecture & Schema

### General Database Rules:

1. **Soft Deletes:** Use `SoftDeletes` on all core business entities (`clients`, `prescriptions`, `orders`, `payments`, `documents`) to ensure historical traceability.
2. **Foreign Key Constraints:** Explicit `cascadeOnDelete()` or `restrictOnDelete()` (especially for orders with payments).
3. **Decimals for Currency:** `DECIMAL(12, 2)` for all monetary fields. Never use `FLOAT` or `DOUBLE`.
4. **Historical Preservability:** When an order is created, product details (unit price, name, optical specifications) are captured snapshot-style into `order_items` so future catalog edits never mutate past orders.

---

### Entities Schema:

#### 1. `users`
- `id` (PK)
- `name` (string)
- `email` (string, unique)
- `password` (string)
- `role` (`UserRole` Enum: `admin`, `optician`, `staff`)
- `is_active` (boolean, default true)
- `remember_token`
- `timestamps`, `soft_deletes`

#### 2. `store_settings`
- `id` (PK)
- `business_name` (string, e.g., "ALBOPTIC")
- `trade_name` (string, nullable)
- `address` (string)
- `city` (string, default "Marrakech")
- `phone` (string)
- `email` (string, nullable)
- `ice` (string, 15 digits - Identifiant Commun de l'Entreprise)
- `if_number` (string, nullable - Identifiant Fiscal)
- `rc_number` (string, nullable - Registre du Commerce)
- `patente` (string, nullable)
- `cnss` (string, nullable)
- `logo_path` (string, nullable)
- `invoice_prefix` (string, default "FAC-")
- `order_prefix` (string, default "BC-")
- `default_tax_rate` (decimal 5,2, default 20.00)
- `currency` (string, default "MAD")
- `invoice_footer_notes` (text, nullable)
- `timestamps`

#### 3. `clients`
- `id` (PK)
- `first_name` (string)
- `last_name` (string)
- `full_name` (string, indexed) — generated or maintained for fast lookup
- `phone` (string, indexed)
- `secondary_phone` (string, nullable)
- `email` (string, nullable)
- `gender` (string, nullable: `male`, `female`, `child`)
- `date_of_birth` (date, nullable)
- `cin` (string, nullable - Carte d'identité nationale)
- `address` (string, nullable)
- `city` (string, default "Marrakech")
- `notes` (text, nullable)
- `timestamps`, `soft_deletes`

#### 4. `prescriptions`
- `id` (PK)
- `client_id` (FK -> `clients.id`, cascade)
- `prescription_date` (date)
- `prescribed_by` (string, nullable - Ophtalmologue / Dr name)
- `doctor_clinic` (string, nullable)
- **OD (Right Eye):**
  - `od_sphere` (decimal 4,2, nullable)
  - `od_cylinder` (decimal 4,2, nullable)
  - `od_axis` (integer 0..180, nullable)
  - `od_addition` (decimal 4,2, nullable)
  - `od_prism` (decimal 4,2, nullable)
  - `od_base` (string, nullable)
  - `od_height` (decimal 4,1, nullable - Hauteur de montage)
- **OG (Left Eye):**
  - `og_sphere` (decimal 4,2, nullable)
  - `og_cylinder` (decimal 4,2, nullable)
  - `og_axis` (integer 0..180, nullable)
  - `og_addition` (decimal 4,2, nullable)
  - `og_prism` (decimal 4,2, nullable)
  - `og_base` (string, nullable)
  - `og_height` (decimal 4,1, nullable - Hauteur de montage)
- **Pupillary Distance (PD):**
  - `pd_total` (decimal 4,1, nullable)
  - `pd_right` (decimal 4,1, nullable)
  - `pd_left` (decimal 4,1, nullable)
  - `pd_near` (decimal 4,1, nullable)
- `prescription_file_path` (string, nullable - scanned prescription image/PDF)
- `notes` (text, nullable)
- `timestamps`, `soft_deletes`

#### 5. `products`
- `id` (PK)
- `sku` (string, nullable, unique)
- `name` (string)
- `category` (`ProductCategory` Enum: `frame`, `lens`, `sunglasses`, `contact_lens`, `accessory`, `service`, `other`)
- `brand` (string, nullable, e.g., "Ray-Ban", "Gucci", "Essilor")
- `model_reference` (string, nullable)
- `color_code` (string, nullable)
- `size` (string, nullable, e.g., "52-18-140")
- `supplier` (string, nullable)
- `cost_price` (decimal 12,2, default 0.00)
- `selling_price` (decimal 12,2, default 0.00)
- `stock_quantity` (integer, default 0)
- `min_stock_alert` (integer, default 2)
- `is_active` (boolean, default true)
- `timestamps`, `soft_deletes`

#### 6. `orders`
- `id` (PK)
- `order_number` (string, unique, indexed - e.g. "BC-2026-000001")
- `client_id` (FK -> `clients.id`, restrict)
- `prescription_id` (FK -> `prescriptions.id`, nullable, set null)
- `order_date` (date)
- `promised_date` (date, nullable - Date prévue de livraison)
- `status` (`OrderStatus` Enum: `draft`, `confirmed`, `in_progress`, `ready`, `delivered`, `cancelled`)
- `subtotal_ht` (decimal 12,2, default 0.00)
- `discount_amount` (decimal 12,2, default 0.00)
- `tax_rate` (decimal 5,2, default 20.00)
- `tax_amount` (decimal 12,2, default 0.00)
- `total_ttc` (decimal 12,2, default 0.00)
- `amount_paid` (decimal 12,2, default 0.00)
- `remaining_amount` (decimal 12,2, default 0.00)
- `payment_status` (`PaymentStatus` Enum: `unpaid`, `partially_paid`, `paid`, `refunded`)
- `notes` (text, nullable)
- `created_by` (FK -> `users.id`, nullable)
- `updated_by` (FK -> `users.id`, nullable)
- `timestamps`, `soft_deletes`

#### 7. `order_items`
- `id` (PK)
- `order_id` (FK -> `orders.id`, cascade)
- `product_id` (FK -> `products.id`, nullable, set null)
- `item_type` (`ItemType` Enum: `frame`, `lens_od`, `lens_og`, `lens_pair`, `sunglasses`, `accessory`, `service`, `contact_lens`, `other`)
- `description` (string)
- `eye` (string, nullable: `OD`, `OG`, `both`)
- `quantity` (integer, default 1)
- `unit_price_ht` (decimal 12,2)
- `discount_amount` (decimal 12,2, default 0.00)
- `tax_rate` (decimal 5,2, default 20.00)
- `subtotal_ht` (decimal 12,2)
- `total_ttc` (decimal 12,2)
- `optical_metadata` (jsonb / json, nullable) — stores snapshot of lens index, treatments, glass brand, frame dimensions
- `timestamps`

#### 8. `payments`
- `id` (PK)
- `order_id` (FK -> `orders.id`, cascade)
- `client_id` (FK -> `clients.id`, restrict)
- `payment_reference` (string, nullable - e.g. receipt/check number)
- `amount` (decimal 12,2)
- `payment_date` (date)
- `payment_method` (`PaymentMethod` Enum: `cash`, `credit_card`, `bank_transfer`, `check`, `virement`, `other`)
- `notes` (string, nullable)
- `created_by` (FK -> `users.id`, nullable)
- `timestamps`, `soft_deletes`

#### 9. `documents`
- `id` (PK)
- `order_id` (FK -> `orders.id`, cascade)
- `document_type` (`DocumentType` Enum: `bon_commande`, `facture`, `recu_paiement`, `devis`)
- `document_number` (string, unique, indexed - e.g. "FAC-2026-000042")
- `generated_at` (datetime)
- `file_path` (string, nullable)
- `total_amount` (decimal 12,2)
- `metadata` (json, nullable)
- `timestamps`, `soft_deletes`

#### 10. `reminders`
- `id` (PK)
- `client_id` (FK -> `clients.id`, cascade)
- `order_id` (FK -> `orders.id`, nullable, set null)
- `reminder_type` (`ReminderType` Enum: `pickup`, `payment_due`, `prescription_renewal`, `follow_up`, `custom`)
- `scheduled_date` (date)
- `status` (`ReminderStatus` Enum: `pending`, `sent`, `completed`, `cancelled`)
- `channel` (string, default "phone_call" - e.g., `phone_call`, `whatsapp_manual`, `sms`)
- `notes` (text, nullable)
- `timestamps`, `soft_deletes`

---

## PHP 8.3 Native Enums Reference

Implement backed string enums for robust typing:

```php
namespace App\Enums;

enum OrderStatus: string {
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case InProgress = 'in_progress';
    case Ready = 'ready';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}

enum PaymentStatus: string {
    case Unpaid = 'unpaid';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Refunded = 'refunded';
}

enum PaymentMethod: string {
    case Cash = 'cash';
    case Card = 'card';
    case BankTransfer = 'bank_transfer';
    case Check = 'check';
    case Other = 'other';
}

enum DocumentType: string {
    case BonCommande = 'bon_commande';
    case Facture = 'facture';
    case Devis = 'devis';
    case Recu = 'recu';
}

enum ReminderType: string {
    case Pickup = 'pickup';
    case PaymentDue = 'payment_due';
    case PrescriptionRenewal = 'prescription_renewal';
    case FollowUp = 'follow_up';
    case Custom = 'custom';
}
```

---

## Financial Rules & Services

Financial correctness is non-negotiable.

### 1. Calculation Formulas:
```text
Item Subtotal HT = (Quantity × Unit Price HT) - Discount
Order Subtotal HT = Sum of (Item Subtotal HT)
Order Discount = Global Order Discount Amount
Taxable Amount HT = Order Subtotal HT - Global Discount
Tax Amount (TVA) = Taxable Amount HT × (Tax Rate / 100)
Total TTC = Taxable Amount HT + Tax Amount
Amount Paid = Sum of all valid Payments
Remaining Balance = Total TTC - Amount Paid
```

### 2. State Rules:
- If `Amount Paid == 0` => `PaymentStatus::Unpaid`
- If `0 < Amount Paid < Total TTC` => `PaymentStatus::PartiallyPaid`
- If `Amount Paid >= Total TTC` => `PaymentStatus::Paid`
- Negative payments are strictly rejected.
- Overpayment is prevented unless explicitly recorded with manager authorization.

### 3. Concurrency & Transactions:
Always wrap order creation, payment addition, and balance recalculation in database transactions:

```php
DB::transaction(function () use ($orderData, $items, $initialPayment) {
    // 1. Lock sequence and create Order
    // 2. Insert OrderItems snapshot
    // 3. Insert Initial Payment if provided
    // 4. Recalculate and update remaining amount & payment status
    // 5. Trigger automated reminders (pickup / balance)
});
```

---

## PDF Documents & Moroccan Legal Requirements

All generated PDFs must render cleanly in A4 format with high-contrast, professional typography.

### 1. Header & Legal Store Identification:
- Store Name & Logo
- Address & City (Marrakech, Morocco)
- Phone & Email
- **ICE (Identifiant Commun de l’Entreprise)** — *Mandatory in Moroccan B2B/B2C invoicing*
- **IF (Identifiant Fiscal)**
- **RC (Registre du Commerce)** & City Tribunal
- **Patente / CNSS** (if configured)

### 2. Bon de Commande (Order Receipt):
- Client Name & Phone
- Order Number (`BC-YYYY-XXXXXX`) and Order Date
- Promised Delivery Date (`Date prévue de mise à disposition`)
- Detailed Prescription Grid:
  - OD: Sphère, Cylindre, Axe, Addition, Hauteur, Prisme
  - OG: Sphère, Cylindre, Axe, Addition, Hauteur, Prisme
  - Écarts pupillaires (PD)
- Items table (Frame, Lenses, Treatments)
- Total TTC, Avance versée (Amount paid), Reste à payer (Remaining balance)
- Barcode/QR code for quick order lookup at the counter.

### 3. Facture (Tax Invoice):
- Unique Invoice Number (`FAC-YYYY-XXXXXX`)
- Invoice Date and Reference to Order Number
- Full Client Name & Address / ICE (if B2B client)
- Detailed line items with HT, TVA rate, and TTC
- Subtotal HT, Remise, TVA amount, Total Net à Payer TTC
- **Total en toutes lettres (Amount written in full French words)** — e.g., *"Arrêtée la présente facture à la somme de : Mille Deux Cents Dirhams TTC."*
- Payment details (Paid amount, Remaining amount, Payment method)

---

## Automated Reminders Engine

The system generates automated pending reminders based on operational triggers:

1. **Ready for Pickup Reminder (`pickup`):**
   - Trigger: When `order.status` transitions to `ready`.
   - Action: Pre-populates a reminder to call or message the client that their glasses are ready.
2. **Balance Follow-up (`payment_due`):**
   - Trigger: When an order is delivered with remaining balance (`partially_paid`).
   - Scheduled: 7–14 days post-delivery.
3. **Annual Vision Checkup (`prescription_renewal`):**
   - Trigger: Created automatically when a prescription/order is completed.
   - Scheduled: 12 months (or 24 months for adults) from the prescription date.

---

## UI / UX & Language Standards

- **User Interface Language:** **French** (Standard in Moroccan optical shops).
- **Codebase / Identifiers:** **English** (Migrations, Models, Controllers, Enums, Variables).

### Official Brand Color Palette & UI Rules:
1. **Red `#E82229`:** Primary buttons, selected navigation accents, and critical alerts. Avoid making every button red.
2. **Navy `#172B4D`:** Sidebar background, primary brand headers, and main titles. Gives the interface a serious, professional medical feel.
3. **Teal `#0F9D91`:** Successful payments, completed tasks, and positive financial indicators. Keep it distinct from the brand red.
4. **White & Light Grey (`#FFFFFF`, `#F8FAFC`, `#F1F5F9`):** Forms, client records, prescriptions, order tables, and dashboard workspaces. Ensures long daily optical working sessions remain comfortable and low eye-strain.
5. **Semantic Status Colors:**
   - **Green / Teal:** Paid / Completed (`PaymentStatus::Paid`).
   - **Amber:** Partially paid / Pending attention (`PaymentStatus::PartiallyPaid`).
   - **Blue:** In progress / Atelier (`OrderStatus::InProgress`).
   - **Red (`#E82229`):** Overdue, errors, or unpaid (`PaymentStatus::Unpaid`).
6. **Prescriptions Grid:** Use a restrained blue/teal accent for the OD/OG sections, but don't rely on color alone to distinguish right and left eyes. Always label them clearly (`OD — Œil Droit`, `OG — Œil Gauche`).
7. **Tactile Buttons, Depth Shadow & Cursor Rule (Touch & Click Feedback):** All buttons and interactive triggers (`button`, `[role="button"]`, submit inputs) must:
   - **Default Resting Shadow:** Display an explicit subtle drop shadow (`box-shadow: 0 1px 3px rgba(0,0,0,0.1)` / `shadow-sm` or colored shadow for primary buttons) to give visual depth and distinct button contour.
   - **Hover Elevation:** Slightly elevate (`translateY(-1px)`) with enhanced shadow (`shadow-md`).
   - **Pointer Cursor:** Strictly enforce a hand cursor (`cursor: pointer`) on interactive states.
   - **Tactile Click/Press Feel (`:active`):** Deliver an authentic tactile depression with `scale(0.975) translateY(1px)` and inward shadow compression (`box-shadow: inset 0 2px 4px rgba(0,0,0,0.18)`).
   - **Disabled State:** Explicit `cursor: not-allowed`, `opacity: 0.6`, with zero transform or active shadow animations.
8. **Hover Color Inversion Rule (عكس لون الخلفية والخط عند التمرير):** On hover across all clickable buttons and action elements:
   - The background color switches to the font color, and the font switches to the background color.
   - **Primary Red (`bg-[#E82229]` with white text):** Inverts on hover to a white background with `#E82229` text and `#E82229` border.
   - **Primary Navy (`bg-[#172B4D]` with white text):** Inverts on hover to a white background with `#172B4D` text and `#172B4D` border.
   - **Primary Teal (`bg-[#0F9D91]` with white text):** Inverts on hover to a white background with `#0F9D91` text and `#0F9D91` border.
   - **Secondary / Slate buttons (Light bg with Navy/Dark text):** Inverts on hover to a solid Navy `#172B4D` background with white text.
   - **Soft Accent / Danger buttons (`bg-rose-50` with red text):** Inverts on hover to a solid Red `#E82229` background with white text.
   - Child SVGs/icons must inherit `currentColor` so both icons and text invert harmoniously.
   - **Navigation Bar / Sidebar Exception:**
     - The **Active Page Link** (e.g. `Ordonnances` when the user is currently on the prescriptions page) remains **strictly stable** with solid Brand Red `#E82229` background and white text/icon. It does **not** invert on hover.
     - Only **Inactive Navigation Links** invert on hover (from slate/transparent text -> solid White background `#FFFFFF` with Navy `#172B4D` text and icon, elevated with subtle shadow).

- **Prescription Terms in UI:**
  - `OD — Œil Droit`, `OG — Œil Gauche`
  - `Sphère`, `Cylindre`, `Axe`, `Addition`, `Écart Pupillaire (EP / PD)`, `Hauteur (H)`
- **Key Navigation & Actions:**
  - `Tableau de bord` (Dashboard)
  - `Clients` (Clients list & profiles)
  - `Nouvelle Commande` (Fast order wizard)
  - `Ordonnances` (Prescriptions)
  - `Paiements & Reste à payer`
  - `Factures & Bons de commande`
  - `Rappels & Tâches` (Reminders)
  - `Analytiques & Chiffre d'affaires`
  - `Paramètres du magasin`

---

## Development Roadmap & Execution Phases

Do NOT attempt to write the entire CRM in one giant step. Implement incrementally and test each milestone:

- **Phase 1: Foundation & Migrations**
  - Database schema, migrations, relationships, Enums, Model factories & seeders.
- **Phase 2: Store Settings & Authentication**
  - Roles, user management, store legal information config.
- **Phase 3: Clients & Prescription Management**
  - Client search/create, Prescription entry grid with OD/OG validation, historical timeline.
- **Phase 4: Product Catalog & Basic Inventory**
  - Frames, lenses, categories, stock tracking.
- **Phase 5: Order Creation Wizard (Core Flow)**
  - Livewire order builder: Client -> Prescription -> Items -> Calculation -> Initial Payment.
- **Phase 6: Payment Tracking & Financial Engine**
  - Multiple payments, partial balances, payment methods, transaction safety.
- **Phase 7: PDF Generation**
  - Bon de Commande and Facture templates with Moroccan legal identifiers and numbers in words.
- **Phase 8: Reminders & Alerts**
  - Pickup alerts, overdue balances, annual renewal reminders.
- **Phase 9: Analytics & Dashboard**
  - Real-time KPIs (Daily sales vs payments received vs outstanding debt).
- **Phase 10: Security, Polish & Automated Tests**
  - Unit tests for `OrderCalculationService`, authorization policies, mobile UX audit.

---

## AI Coding Agent Behavioral Rules

1. **Check Existing Implementations First:** Inspect the workspace and existing migrations before writing duplicate code.
2. **Never Hardcode Store Data:** Pull store name, ICE, RC, address, and VAT rates from `store_settings`.
3. **Enforce Type Safety:** Use strict types `declare(strict_types=1);` and PHP 8.3 Enums.
4. **Preserve Business History:** Never overwrite historical prescriptions or past order line item prices when catalog products change.
5. **Always Verify Calculations Server-Side:** Never rely on Javascript totals submitted from the client.
