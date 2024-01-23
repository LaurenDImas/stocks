<?php

namespace Database\Seeders;

use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'barcode'   => "Cup 14 oz-00",
                'name' => 'Cup 14 oz',
                'category' => 'Packaging',
                'unit'=>"Pack"
            ],
            [
                'barcode'   => "Cup 22 oz-00",
                'name' => 'Cup 22 oz',
                'category' => 'Packaging',
                'unit'=>"Pack"
            ],
            [
                'barcode'   => "Cup 16 oz-00",
                'name' => 'Cup 16 oz',
                'category' => 'Packaging',
                'unit'=>"Pack"
            ],
            [
                'barcode'   => "Cup 08 oz-00",
                'name' => 'Cup 08 oz',
                'category' => 'Packaging',
                'unit'=>"Pack"
            ],
            [
                'barcode'   => "Plastik S-00",
                'name' => 'Plastik take way S',
                'category' => 'Packaging',
                'unit'=>"Pack"
            ],
            [
                'barcode'   => "Plastik M-00",
                'name' => 'Plastik take way M',
                'category' => 'Packaging',
                'unit'=>"Pack"
            ],
            [
                'barcode'   => "Plastik L-00",
                'name' => 'Plastik take way L',
                'category' => 'Packaging',
                'unit'=>"Pack"
            ],
            [
                'barcode'   => "Seal-00",
                'name' => 'Seal',
                'category' => 'Packaging',
                'unit'=>"Pack"
            ],
            [
                'barcode'   => "Sedotan-00",
                'name' => 'Sedotan',
                'category' => 'Packaging',
                'unit'=>"Pack"
            ],
            [
                'barcode'   => "Black tea-01",
                'name' => 'Black tea',
                'category' => 'Raw Material',
                'unit'=>'GR',
            ],
            [
                'barcode'   => "Jamine tea-01",
                'name' => 'Jamine tea',
                'category' => 'Raw Material',
                'unit'=>'GR',
            ],
            [
                'barcode'   => "Thai tea-01",
                'name' => 'Thai tea',
                'category' => 'Raw Material',
                'unit'=>'GR',
            ],
            [
                'barcode'   => "Thai green tea-01",
                'name' => 'Thai green tea',
                'category' => 'Raw Material',
                'unit'=>'GR',
            ],
            [
                'barcode'   => "robusta-01",
                'name' => 'Kopi robusta',
                'category' => 'Raw Material',
                'unit'=>'GR',
            ],
            [
                'barcode'   => "Arabica-01",
                'name' => 'Kopi arabica',
                'category' => 'Raw Material',
                'unit'=>'GR',
            ],
            [
                'barcode'   => "Coklat-01",
                'name' => 'Coklat powder',
                'category' => 'Raw Material',
                'unit'=>'GR',
            ],
            [
                'barcode'   => "Matcha-01",
                'name' => 'Matcha powder',
                'category' => 'Raw Material',
                'unit'=>'GR',
            ],
            [
                'barcode'   => "EvaporatedMilk-02",
                'name' => 'Evaporated milk',
                'category' => 'Fresh Milk',
                'unit'=>'ML'
            ],
            [
                'barcode'   => "Aren-02",
                'name' => 'Gula aren',
                'category' => 'Fresh Milk',
                'unit'=>'ML'
            ],
            [
                'barcode'   => "Vanila-02",
                'name' => 'Vanila sirup',
                'category' => 'Fresh Milk',
                'unit'=>'ML'
            ],
            [
                'barcode'   => "Caramel-02",
                'name' => 'Caramel sirup',
                'category' => 'Fresh Milk',
                'unit'=>'ML'
            ],
            [
                'barcode'   => "Hazelnut-02",
                'name' => 'Hazelnut sirup',
                'category' => 'Fresh Milk',
                'unit'=>'ML'    
            ]
        ];

        collect($items)->each(function ($result) { Item::create($result); });
    }
}
