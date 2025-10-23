<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PreorderTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create test users
        $customer = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'John Customer',
                'password' => Hash::make('password'),
                'user_type' => 'customer',
                'phone' => '+1234567890',
                'email_verified_at' => now(),
            ]
        );

        $wholesaler = User::firstOrCreate(
            ['email' => 'wholesaler@test.com'],
            [
                'name' => 'Jane Wholesaler',
                'password' => Hash::make('password'),
                'user_type' => 'wholesaler',
                'phone' => '+1234567891',
                'business_name' => 'Wholesale Business Inc.',
                'email_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now(),
            ]
        );

        // Get or create a sample product
        $product = Product::first();
        if (!$product) {
            echo "No products found. Please create some products first.\n";
            return;
        }

        // Create sample pre-orders
        $preorders = [
            [
                'user_id' => $customer->id,
                'guest_id' => null,
                'shipping_address' => json_encode([
                    'name' => 'John Customer',
                    'email' => 'customer@test.com',
                    'phone' => '+1234567890',
                    'address' => '123 Main St, City, State 12345'
                ]),
                'grand_total' => 150.00,
                'is_preorder' => true,
                'preorder_status' => 'confirmed',
                'paid_amount' => 75.00,
                'payment_status' => 'partial_paid',
                'delivery_status' => 'pending',
                'delivery_date' => Carbon::now()->addDays(15),
                'delivery_notes' => 'Please call before delivery',
                'delivery_location' => 'Front door',
                'preorder_notes' => 'Customer preorder - 50% advance payment',
                'confirmed_at' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $wholesaler->id,
                'guest_id' => null,
                'shipping_address' => json_encode([
                    'name' => 'Jane Wholesaler',
                    'email' => 'wholesaler@test.com',
                    'phone' => '+1234567891',
                    'address' => '456 Business Ave, City, State 12345'
                ]),
                'grand_total' => 500.00,
                'is_preorder' => true,
                'preorder_status' => 'pending',
                'paid_amount' => 100.00,
                'payment_status' => 'partial_paid',
                'delivery_status' => 'pending',
                'delivery_date' => Carbon::now()->addDays(20),
                'delivery_notes' => 'Bulk order - warehouse delivery',
                'delivery_location' => 'Loading dock',
                'preorder_notes' => 'Wholesaler bulk order - 20% advance payment',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => null,
                'guest_id' => 'guest_' . uniqid(),
                'shipping_address' => json_encode([
                    'name' => 'Guest Customer',
                    'email' => 'guest@example.com',
                    'phone' => '+1234567892',
                    'address' => '789 Guest St, City, State 12345'
                ]),
                'grand_total' => 75.00,
                'is_preorder' => true,
                'preorder_status' => 'product_arrived',
                'paid_amount' => 75.00,
                'payment_status' => 'paid',
                'delivery_status' => 'pending',
                'delivery_date' => Carbon::now()->addDays(5),
                'delivery_notes' => 'Guest order - full payment received',
                'delivery_location' => 'Reception desk',
                'preorder_notes' => 'Guest preorder - full advance payment',
                'product_arrived_at' => Carbon::now()->subDays(1),
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => $customer->id,
                'guest_id' => null,
                'shipping_address' => json_encode([
                    'name' => 'John Customer',
                    'email' => 'customer@test.com',
                    'phone' => '+1234567890',
                    'address' => '123 Main St, City, State 12345'
                ]),
                'grand_total' => 200.00,
                'is_preorder' => true,
                'preorder_status' => 'completed',
                'paid_amount' => 200.00,
                'payment_status' => 'paid',
                'delivery_status' => 'delivered',
                'delivery_date' => Carbon::now()->subDays(2),
                'delivery_notes' => 'Delivered successfully',
                'delivery_location' => 'Front door',
                'preorder_notes' => 'Completed preorder',
                'confirmed_at' => Carbon::now()->subDays(10),
                'product_arrived_at' => Carbon::now()->subDays(5),
                'completed_at' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(2),
            ]
        ];

        foreach ($preorders as $orderData) {
            $order = Order::create($orderData);

            // Create order detail for each order
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $orderData['grand_total'],
                'tax' => 0,
                'shipping_cost' => 0,
                'quantity' => 1,
                'payment_status' => $orderData['payment_status'],
                'delivery_status' => $orderData['delivery_status'],
            ]);
        }

        echo "Sample pre-order data created successfully!\n";
        echo "Created " . count($preorders) . " sample pre-orders.\n";
    }
}
