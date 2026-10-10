# ALBOPTIC CRM — Architecture & Technical Blueprint

## 1. System Overview
**ALBOPTIC CRM** is an optician-specific business management system designed for **ALBOPTIC** (Marrakech, Morocco). It manages optical clients, clinical eye prescriptions, prescription-based orders, inventory snapshots, partial and full payments, Moroccan tax-compliant document generation (Bon de Commande & Facture), automated customer reminders, and financial KPIs.

---

## 2. Entity-Relationship Diagram (ERD)

```mermaid
erDiagram
    STORE_SETTINGS {
        bigint id PK
        string business_name
        string trade_name
        string address
        string city
        string phone
        string email
        string ice
        string rc
        string tax_identifier
        string logo_path
        string invoice_prefix
        string order_prefix
        decimal default_tax_rate
        string currency
    }

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role
        boolean is_active
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    CLIENTS {
        bigint id PK
        string first_name
        string last_name
        string full_name "Indexed"
        string phone "Indexed"
        string email
        string gender
        date date_of_birth
        string address
        string city
        text notes
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    PRESCRIPTIONS {
        bigint id PK
        bigint client_id FK
        date prescription_date
        decimal od_sphere
        decimal od_cylinder
        int od_axis
        decimal od_addition
        decimal og_sphere
        decimal og_cylinder
        int og_axis
        decimal og_addition
        decimal pd
        decimal pd_right
        decimal pd_left
        string prescription_file_path
        string prescribed_by
        text notes
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    PRODUCTS {
        bigint id PK
        string sku UK
        string name
        string category
        text description
        string brand
        string supplier
        decimal cost_price
        decimal selling_price
        int stock_quantity
        boolean is_active
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    ORDERS {
        bigint id PK
        bigint client_id FK
        bigint prescription_id FK
        string order_number UK "Indexed"
        date order_date
        string status
        decimal subtotal
        decimal discount
        decimal tax_rate
        decimal tax_amount
        decimal total_amount
        decimal amount_paid
        decimal remaining_amount
        string payment_status
        text notes
        bigint created_by FK
        bigint updated_by FK
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        string item_type
        string description
        int quantity
        decimal unit_price
        decimal discount
        decimal tax_rate
        decimal subtotal
        decimal total
        json metadata
        timestamp created_at
        timestamp updated_at
    }

    PAYMENTS {
        bigint id PK
        bigint order_id FK
        decimal amount
        date payment_date
        string payment_method
        string reference
        text notes
        bigint created_by FK
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    DOCUMENTS {
        bigint id PK
        bigint order_id FK
        string document_type
        string document_number UK "Indexed"
        string file_path
        timestamp generated_at
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    REMINDERS {
        bigint id PK
        bigint client_id FK
        bigint order_id FK
        string reminder_type
        date reminder_date
        string status
        text notes
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    CLIENTS ||--o{ PRESCRIPTIONS : "has many"
    CLIENTS ||--o{ ORDERS : "has many"
    CLIENTS ||--o{ REMINDERS : "has many"
    PRESCRIPTIONS ||--o{ ORDERS : "referenced by"
    ORDERS ||--|{ ORDER_ITEMS : "has many"
    ORDERS ||--o{ PAYMENTS : "has many"
    ORDERS ||--o{ DOCUMENTS : "has many"
    ORDERS ||--o{ REMINDERS : "has many"
    PRODUCTS ||--o{ ORDER_ITEMS : "referenced by"
    USERS ||--o{ ORDERS : "created / updated by"
    USERS ||--o{ PAYMENTS : "created by"
```

---

## 3. Application Directory Structure

```text
app/
├── Enums/
│   ├── DocumentType.php
│   ├── ItemType.php
│   ├── OrderStatus.php
│   ├── PaymentMethod.php
│   ├── PaymentStatus.php
│   ├── ProductCategory.php
│   ├── ReminderStatus.php
│   ├── ReminderType.php
│   └── UserRole.php
├── Models/
│   ├── Client.php
│   ├── Document.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Payment.php
│   ├── Prescription.php
│   ├── Product.php
│   ├── Reminder.php
│   ├── StoreSetting.php
│   └── User.php
└── Services/
    ├── DocumentNumberGeneratorService.php
    └── OrderCalculationService.php
database/
├── factories/
├── migrations/
└── seeders/
```

---

## 4. Main Planned Routes & Modules

| Module | Purpose | Main Actions |
|---|---|---|
| **Dashboard** (`/`) | Live KPIs & Counter Activity | Daily sales, payments received, pending debts, today's pickups & reminders |
| **Clients** (`/clients`) | Customer Directory & History | Search by name/phone, create profile, view prescription & order history |
| **Prescriptions** (`/prescriptions`) | Clinical Refraction Data | Record OD/OG, PD, axis, addition, attach scanned Rx, timeline view |
| **Orders** (`/orders`) | Fast Counter Order Wizard | Select client & Rx, add frames & lenses, apply discount, record down payment |
| **Payments** (`/payments`) | Financial Settlements | Record multi-stage payments, view partial balances, filter by method |
| **Documents** (`/documents`) | Tax Invoicing & Receipts | Generate PDF Bon de Commande and Facture with Moroccan ICE/IF/RC |
| **Products** (`/products`) | Basic Optical Inventory | Frames, lenses, sunglasses, accessories, stock thresholds |
| **Reminders** (`/reminders`) | Customer Follow-up Engine | Pickup notifications, payment due reminders, annual vision checkups |
| **Settings** (`/settings`) | Shop Legal & Tax Configuration | ICE, RC, IF, address, default TVA (20%), invoice prefixes |

---

## 5. Development Conventions

1. **Strict Types & Native Enums:** Every PHP class uses `declare(strict_types=1);` and PHP 8.2+ backed enums.
2. **Financial Precision:** Currency fields use `DECIMAL(12,2)`. All financial math is rounded to 2 decimal places using `OrderCalculationService`.
3. **Audit Trail & Soft Deletes:** Core business records use `SoftDeletes` to preserve financial and medical history.
4. **Moroccan Invoicing Compliance:** Store legal identifiers (`ICE`, `RC`, `IF`, `Patente`) are dynamically pulled from `StoreSetting`.
