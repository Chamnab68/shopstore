<?php

namespace Database\Seeders;

use App\Models\{User, Category, Product, Banner, ApiKey};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name'     => 'Admin ShopStore',
            'email'    => 'admin@shopstore.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '012 345 678',
            'address'  => 'Phnom Penh, Cambodia',
            'is_active'=> true,
        ]);

        // Sample customer
        User::create([
            'name'     => 'Test Customer',
            'email'    => 'customer@shopstore.com',
            'password' => Hash::make('password'),
            'role'     => 'customer',
            'phone'    => '098 765 432',
            'is_active'=> true,
        ]);

        // Generate API key for admin
        ApiKey::create([
            'name'        => 'Default Public API Key',
            'key'         => 'sk-' . bin2hex(random_bytes(20)),
            'user_id'     => $admin->id,
            'permissions' => ['read'],
            'is_active'   => true,
        ]);

        // Categories
        $categories = [
            ['name'=>'Fruits & Vegetables',  'slug'=>'fruits-vegetables',  'image'=>null],
            ['name'=>'Baby & Pregnancy',      'slug'=>'baby-pregnancy',     'image'=>null],
            ['name'=>'Beverages',             'slug'=>'beverages',          'image'=>null],
            ['name'=>'Meats & Seafood',       'slug'=>'meats-seafood',      'image'=>null],
            ['name'=>'Biscuits & Snacks',     'slug'=>'biscuits-snacks',    'image'=>null],
            ['name'=>'Breads & Bakery',       'slug'=>'breads-bakery',      'image'=>null],
            ['name'=>'Breakfast & Dairy',     'slug'=>'breakfast-dairy',    'image'=>null],
            ['name'=>'Frozen Foods',          'slug'=>'frozen-foods',       'image'=>null],
            ['name'=>'Grocery & Staples',     'slug'=>'grocery-staples',    'image'=>null],
        ];

        $createdCats = [];
        foreach ($categories as $i => $cat) {
            $createdCats[] = Category::create(array_merge($cat, ['is_active'=>true,'sort_order'=>$i]));
        }

        // Products
        $products = [
            ['name'=>'100 Percent Apple Juice – 64 fl oz Bottle','sku'=>'BEV-001','category'=>2,'price'=>1.99,'sale_price'=>0.50,'stock'=>23,'unit'=>'bottle','is_organic'=>true,'is_featured'=>true,'rating'=>4.0],
            ['name'=>'Simply Orange Pulp Free Juice – 52 fl oz','sku'=>'BEV-002','category'=>2,'price'=>4.13,'sale_price'=>2.45,'stock'=>68,'unit'=>'bottle','rating'=>4.0],
            ['name'=>'Vitaminwater Zero Sugar Squeezed Electrolyte Enhanced Drink','sku'=>'BEV-003','category'=>2,'price'=>8.99,'sale_price'=>4.99,'stock'=>52,'unit'=>'6pk','rating'=>3.5],
            ['name'=>'Great Value Rising Crust Frozen Pizza, Supreme','sku'=>'FRZ-001','category'=>7,'price'=>9.99,'sale_price'=>8.99,'stock'=>87,'unit'=>'pcs','is_cold_sale'=>true,'is_featured'=>true,'rating'=>5.0],
            ['name'=>'California Pizza Kitchen Margherita, Crispy Thin Crust','sku'=>'FRZ-002','category'=>7,'price'=>14.77,'sale_price'=>11.77,'stock'=>19,'unit'=>'pcs','is_cold_sale'=>true,'rating'=>4.0],
            ['name'=>'Cantaloupe Melon Fresh Organic Cut','sku'=>'FV-001','category'=>0,'price'=>2.98,'sale_price'=>1.25,'stock'=>86,'unit'=>'pcs','is_organic'=>true,'is_featured'=>true,'rating'=>4.5],
            ['name'=>'Real Plant-Powered Protein Shake – Double Chocolate','sku'=>'BEV-004','category'=>2,'price'=>17.89,'sale_price'=>14.89,'stock'=>76,'unit'=>'pcs','rating'=>3.0],
            ['name'=>'Absolut Grapefruit Paloma Sparkling Vodka Cocktail 4pk 355ml','sku'=>'BEV-005','category'=>2,'price'=>9.99,'sale_price'=>6.99,'stock'=>38,'unit'=>'4pk','is_featured'=>true,'rating'=>4.5],
            ['name'=>'Angel Soft Toilet Paper, 9 Mega Rolls','sku'=>'GRO-001','category'=>8,'price'=>17.12,'sale_price'=>14.12,'stock'=>88,'unit'=>'pack','rating'=>4.0],
            ['name'=>'Simple Kitchen FD Sliced Strawberries – 1.08lb','sku'=>'FV-002','category'=>0,'price'=>24.90,'sale_price'=>21.90,'stock'=>50,'unit'=>'bag','rating'=>4.0],
            ['name'=>'Red Baron Frozen Hand Tossed Ultimate Pepperoni Pizza','sku'=>'FRZ-003','category'=>7,'price'=>19.99,'sale_price'=>14.99,'stock'=>45,'unit'=>'pcs','is_cold_sale'=>true,'rating'=>4.5],
            ['name'=>'Oscar Mayer Ham & Swiss Melt Scrambler – 3oz','sku'=>'MT-001','category'=>3,'price'=>12.90,'sale_price'=>11.90,'stock'=>30,'unit'=>'pcs','rating'=>4.0],
            ['name'=>'Large Garden Spinach & Herb Wrap Tortillas – 15oz 8ct','sku'=>'BR-001','category'=>5,'price'=>32.90,'sale_price'=>27.90,'stock'=>25,'unit'=>'pack','rating'=>4.5],
            ['name'=>'Great Value Rising Crust Pizza, Cheese, 27.4 oz','sku'=>'FRZ-004','category'=>7,'price'=>14.89,'sale_price'=>12.89,'stock'=>60,'unit'=>'pcs','rating'=>3.5],
            ['name'=>'TeeChia Sustained Energy Cereal – Blueberry Date 300g','sku'=>'BD-001','category'=>6,'price'=>26.67,'sale_price'=>21.67,'stock'=>40,'unit'=>'bag','is_organic'=>true,'is_featured'=>true,'rating'=>4.5],
            ['name'=>'Organic Banana Bundle – 1lb','sku'=>'FV-003','category'=>0,'price'=>2.50,'sale_price'=>null,'stock'=>100,'unit'=>'lb','is_organic'=>true,'rating'=>4.8],
            ['name'=>'Whole Milk – 1 Gallon','sku'=>'BD-002','category'=>6,'price'=>5.99,'sale_price'=>null,'stock'=>80,'unit'=>'gallon','rating'=>4.5],
            ['name'=>'Sourdough Bread Loaf 24oz','sku'=>'BR-002','category'=>5,'price'=>4.49,'sale_price'=>3.99,'stock'=>35,'unit'=>'loaf','rating'=>4.7],
        ];

        foreach ($products as $p) {
            $catIndex = $p['category'];
            $category = $createdCats[$catIndex] ?? $createdCats[0];
            $slug = Str::slug($p['name']);
            $count = 1;
            $baseSlug = $slug;
            while (Product::where('slug',$slug)->exists()) { $slug = $baseSlug.'-'.$count++; }

            Product::create([
                'category_id'  => $category->id,
                'name'         => $p['name'],
                'slug'         => $slug,
                'sku'          => $p['sku'],
                'price'        => $p['price'],
                'sale_price'   => $p['sale_price'] ?? null,
                'stock'        => $p['stock'],
                'unit'         => $p['unit'],
                'is_active'    => true,
                'is_featured'  => $p['is_featured'] ?? false,
                'is_organic'   => $p['is_organic'] ?? false,
                'is_cold_sale' => $p['is_cold_sale'] ?? false,
                'rating'       => $p['rating'],
                'review_count' => rand(2,10),
                'sold_count'   => rand(10,500),
                'description'  => 'Vivamus adipiscing nisl ut dolor dignissim semper. Nulla luctus malesuada tincidunt. Class aptent taciti sociosqu ad litora torquent Vivamus adipiscing nisl ut dolor dignissim semper.',
                'short_description' => 'Premium quality product at the best price.',
            ]);
        }

        // Banners
        Banner::create([
            'title'          => 'Shopping with us for better quality and the best price',
            'subtitle'       => 'We have prepared special discounts for you on grocery products. Don\'t miss these opportunities...',
            'badge'          => 'Weekend Discount',
            'price'          => '21.67',
            'original_price' => '26.67',
            'link'           => '/products',
            'is_active'      => true,
            'sort_order'     => 1,
        ]);
        Banner::create([
            'title'          => 'Fresh Organic Produce Delivered Daily',
            'subtitle'       => 'Farm to table freshness guaranteed every single day.',
            'badge'          => 'Daily Fresh',
            'price'          => '5.99',
            'original_price' => '9.99',
            'link'           => '/products?category=fruits-vegetables',
            'is_active'      => true,
            'sort_order'     => 2,
        ]);

        echo "✅ Database seeded successfully!\n";
        echo "Admin: admin@shopstore.com / password\n";
        echo "Customer: customer@shopstore.com / password\n";
    }
}
