<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use Illuminate\Support\Carbon;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Medicines' => [
                ['Paracetamol 500mg', 'tablet'],
                ['Ibuprofen 400mg', 'tablet'],
                ['Amoxicillin 500mg', 'capsule'],
                ['Cetirizine 10mg', 'tablet'],
                ['Metformin 500mg', 'tablet'],
                ['Losartan 50mg', 'tablet'],
                ['Salbutamol Syrup', 'bottles'],
                ['Oral Rehydration Salts', 'sachets'],
                ['Omeprazole 20mg', 'capsule'],
                ['Multivitamin Syrup', 'bottles'],
            ],
            'Medical Supplies' => [
                ['Sterile Gauze 4x4', 'packs'],
                ['Adhesive Bandage Strip', 'boxes'],
                ['Cotton Balls', 'packs'],
                ['Disposable Gloves (Medium)', 'boxes'],
                ['Face Masks (Surgical)', 'boxes'],
                ['Alcohol 70%', 'bottles'],
                ['Betadine Solution', 'bottles'],
                ['Micropore Tape', 'rolls'],
                ['Suture Set', 'sets'],
                ['Thermometer Probe Covers', 'boxes'],
            ],
            'Syringes & Needles' => [
                ['Syringe 1 mL', 'boxes'],
                ['Syringe 3 mL', 'boxes'],
                ['Syringe 5 mL', 'boxes'],
                ['Syringe 10 mL', 'boxes'],
                ['Needle 21G', 'boxes'],
                ['Needle 23G', 'boxes'],
                ['Insulin Syringe', 'boxes'],
                ['Safety Needle 23G', 'boxes'],
                ['IV Cannula 22G', 'boxes'],
                ['IV Cannula 24G', 'boxes'],
            ],
            'Vaccines' => [
                ['Tetanus Toxoid Vaccine', 'vials'],
                ['Hepatitis B Vaccine', 'vials'],
                ['MMR Vaccine', 'vials'],
                ['Influenza Vaccine', 'vials'],
                ['COVID-19 Vaccine', 'vials'],
                ['DPT Vaccine', 'vials'],
                ['BCG Vaccine', 'vials'],
                ['Polio Vaccine (OPV)', 'vials'],
                ['Pneumococcal Vaccine', 'vials'],
                ['HPV Vaccine', 'vials'],
            ],
            'PPE' => [
                ['Nitrile Gloves (Medium)', 'boxes'],
                ['N95 Respirator Mask', 'boxes'],
                ['Face Shield', 'pieces'],
                ['Disposable Gown', 'packs'],
                ['Shoe Cover', 'packs'],
                ['Head Cap', 'packs'],
                ['Isolation Gown', 'pieces'],
                ['Protective Goggles', 'pieces'],
                ['Hazmat Suit', 'pieces'],
                ['Apron (Disposable)', 'packs'],
            ],
        ];

        foreach ($categories as $category => $items) {
            foreach ($items as [$name, $unit]) {
                $currentStock = rand(20, 150);
                $minStock = rand(5, 20);

                $expiry = null;
                if (in_array($category, ['Medicines', 'Vaccines', 'Medical Supplies'])) {
                    $expiry = Carbon::now()->addMonths(rand(3, 24));
                }

                $item = Inventory::create([
                    'item_name' => $name,
                    'description' => null,
                    'category' => $category,
                    'current_stock' => $currentStock,
                    'minimum_stock' => $minStock,
                    'unit' => $unit,
                    'unit_price' => rand(50, 500) / 1,
                    'expiry_date' => $expiry,
                    'supplier' => null,
                    'location' => 'Clinic Storage',
                    'status' => 'in_stock',
                ]);

                $item->updateStatus();
            }
        }
    }
}
