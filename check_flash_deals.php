<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FlashDeal;

echo "Updating flash deal product discounts...\n\n";

$activeDeal = FlashDeal::where('status', 1)
    ->where('start_date', '<=', time())
    ->where('end_date', '>=', time())
    ->first();

if ($activeDeal) {
    echo "Found active deal: {$activeDeal->title} (ID: {$activeDeal->id})\n";
    
    // Get flash deal products
    $flashDealProducts = \DB::table('flash_deal_products')
        ->where('flash_deal_id', $activeDeal->id)
        ->get();
    
    echo "Products in this flash deal: " . $flashDealProducts->count() . "\n\n";
    
    if ($flashDealProducts->count() > 0) {
        foreach ($flashDealProducts as $fdp) {
            echo "Product ID: {$fdp->product_id}\n";
            echo "Current discount: {$fdp->discount}%, Type: {$fdp->discount_type}\n";
            
            // Update with meaningful discount values
            $newDiscount = rand(10, 30); // Random discount between 10-30%
            $discountType = 'percent';
            
            \DB::table('flash_deal_products')
                ->where('id', $fdp->id)
                ->update([
                    'discount' => $newDiscount,
                    'discount_type' => $discountType
                ]);
            
            echo "Updated discount: {$newDiscount}%, Type: {$discountType}\n";
            echo "---\n";
        }
        
        echo "All flash deal product discounts have been updated!\n";
        
    } else {
        echo "No products found in this flash deal!\n";
    }
    
} else {
    echo "No active flash deals found.\n";
}