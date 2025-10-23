<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\BusinessSetting; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WholesalerController extends Controller
{
    public function all_requests()
    {
        $pending_wholesalers = User::where('user_type', 'wholesaler')
                                   ->where('status', 'pending')
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(15);
                                //    ->get();

        return view('backend.wholesale.requests', compact('pending_wholesalers'));
    }

     public function all_wholesalers()
    {
        $all_wholesalers = User::where('user_type', 'wholesaler')
                               ->orderBy('created_at', 'desc')
                               ->paginate(15);
                            //    ->get();

        return view('backend.wholesale.all_wholesalers', compact('all_wholesalers'));
    }

     public function approve_request($id)
    {
        $user = User::findOrFail($id);
        if ($user->user_type == 'wholesaler' && $user->status == 'pending') {
            $user->status = 'active'; // স্ট্যাটাস 'active' করা হলো
            $user->save();

            // সফল বার্তা
            flash(translate('Wholesaler request approved successfully.'))->success();

            // TODO: Wholesaler কে ইমেইল নোটিফিকেশন পাঠানোর লজিক এখানে যোগ করুন।
            // Mail::to($user->email)->send(new WholesalerApprovedMail($user));

        } else {
            flash(translate('Invalid wholesaler request or already processed.') . ' Status: ' . $user->status)->warning();
        }
        return back(); // আগের পেজে রিডাইরেক্ট করা হচ্ছে
    }

        public function reject_request($id)
    {
        $user = User::findOrFail($id);
        if ($user->user_type == 'wholesaler' && $user->status == 'pending') {
            $user->status = 'rejected'; // স্ট্যাটাস 'rejected' করা হলো
            $user->save();

            // সতর্কবার্তা
            flash(translate('Wholesaler request rejected successfully.'))->warning();

            // TODO: Wholesaler কে ইমেইল নোটিফিকেশন পাঠানোর লজিক এখানে যোগ করুন।
            // Mail::to($user->email)->send(new WholesalerRejectedMail($user));

        } else {
            flash(translate('Invalid wholesaler request or already processed.') . ' Status: ' . $user->status)->warning();
        }
        return back();
    }

    /**
     * এই ফাংশনটি নির্দিষ্ট একটি আবেদনকে ডেটাবেজ থেকে সম্পূর্ণ মুছে ফেলে।
     */
      public function delete_request($id)
    {
        $user = User::where('id', $id)->where('user_type', 'wholesaler')->firstOrFail(); // নিশ্চিত করা হলো যে শুধু হোলসেলার অ্যাকাউন্ট ডিলিট করা হচ্ছে
        $user->delete();

        // সফল বার্তা
        flash(translate('Wholesaler account deleted successfully.'))->success();
        return back();
    }




      public function wholesale_orders_index(Request $request)
    {
        // Wholesaler ইউজার আইডিগুলো সংগ্রহ করা হচ্ছে
        $wholesaler_ids = User::where('user_type', 'wholesaler')->pluck('id');

        // Wholesaler দের করা অর্ডারগুলো লোড করা হচ্ছে
        // আপনার Order মডেলের নাম এবং wholesale order চিহ্নিত করার লজিক প্রয়োজন হতে পারে।
        // ধরে নেওয়া হচ্ছে যে, wholesaler_ids থেকে অর্ডারগুলোই পাইকারি অর্ডার।
        $wholesale_orders = Order::whereIn('user_id', $wholesaler_ids)
                                ->orderBy('created_at', 'desc')
                                ->paginate(15); // Pagination যোগ করা হলো

        return view('backend.wholesale.orders.index', compact('wholesale_orders'));
    }

     public function wholesale_order_show($id)
    {
        // অর্ডার আইডি ডিক্রিপ্ট করা হচ্ছে যদি আপনার সিস্টেম encrypt ব্যবহার করে
        $decrypted_id = decrypt($id);
        $order = Order::findOrFail($decrypted_id);

        // নিশ্চিত করা হলো যে অর্ডারটি একজন হোলসেলার ইউজার করেছে
        if ($order->user && $order->user->user_type === 'wholesaler') {
            // এখানে আপনি অর্ডারের বিস্তারিত দেখানোর জন্য একটি ডেডিকেটেড ভিউ ব্যবহার করতে পারেন
            // অথবা আপনার বিদ্যমান অর্ডার ডিটেইলস মডাল বা ভিউ রিইউজ করতে পারেন।
            // উদাহরণস্বরূপ, এখানে একটি নতুন ভিউ 'backend.wholesale.orders.show' ব্যবহার করা হচ্ছে।
            return view('backend.wholesale.orders.show', compact('order'));
        }

        flash(translate('Wholesale order not found or not a wholesale order.'));
        abort(404); // যদি অর্ডারটি না পাওয়া যায় বা এটি পাইকারি অর্ডার না হয়
    }

 public function wholesale_settings_index()
    {
        // বিদ্যমান সেটিংস লোড করা হচ্ছে।
        // 'business_settings' টেবিলে আপনি এই সেটিংসগুলো সংরক্ষণ করবেন।
        // যদি সেটিংস না থাকে, তবে একটি ডিফল্ট মান পাঠানো যেতে পারে।
        $wholesale_min_order_quantity = BusinessSetting::where('type', 'wholesale_min_order_quantity')->first()->value ?? 0;
        $wholesale_discount_percentage = BusinessSetting::where('type', 'wholesale_discount_percentage')->first()->value ?? 0;
        // এখানে অন্যান্য পাইকারি সেটিংস লোড করুন

        return view('backend.wholesale.settings.index', compact('wholesale_min_order_quantity', 'wholesale_discount_percentage'));
    }

     public function wholesale_settings_update(Request $request)
    {
        // রিকোয়েস্ট ডেটা ভ্যালিডেট করা হচ্ছে
        $request->validate([
            'wholesale_min_order_quantity' => 'nullable|numeric|min:0',
            'wholesale_discount_percentage' => 'nullable|numeric|min:0|max:100',
            // এখানে অন্যান্য পাইকারি-নির্দিষ্ট সেটিংসের জন্য ভ্যালিডেশন যোগ করুন
        ]);

        // সেটিংস ডাটাবেসে সংরক্ষণ করা হচ্ছে (BusinessSetting মডেল ব্যবহার করে)
        BusinessSetting::updateOrCreate(
            ['type' => 'wholesale_min_order_quantity'],
            ['value' => $request->wholesale_min_order_quantity]
        );
        BusinessSetting::updateOrCreate(
            ['type' => 'wholesale_discount_percentage'],
            ['value' => $request->wholesale_discount_percentage]
        );
        // এখানে অন্যান্য পাইকারি সেটিংস আপডেট করুন

        flash(translate('Wholesale settings updated successfully'))->success();
        return back();
    }

     public function all_wholesale_products(Request $request)
    {
        $sort_by = null;
        $search = null;

        // পাইকারি পণ্যগুলো লোড করা হচ্ছে।
        // এখানে ধরে নেওয়া হচ্ছে যে Product মডেলে 'is_wholesale' নামে একটি boolean ফিল্ড আছে।
        // যদি আপনার পাইকারি পণ্য চিহ্নিত করার অন্য কোনো লজিক থাকে (যেমন, ProductVariant এ wholesale price থাকলে),
        // তাহলে সেই অনুযায়ী কোয়েরি পরিবর্তন করতে হবে।
        $products = Product::where('wholesale_product', 1); // ধরে নেওয়া হলো 'wholesale_product' কলামটি পাইকারি পণ্য চিহ্নিত করে
                                  // অথবা, যদি কোনো অ্যাট্রিব্যুট দিয়ে আলাদা করা না যায়, তবে সব প্রোডাক্টই লোড করে ফ্রন্টএন্ডে পাইকারি মূল্য দেখানোর লজিক থাকতে পারে।

        if ($request->search != null) {
            $search = $request->search;
            $products = $products->where('name', 'like', '%' . $search . '%');
        }
        if ($request->sort_by != null) {
            $sort_by = $request->sort_by;
            $products = $products->orderBy($sort_by, 'desc'); // উদাহরণস্বরূপ, 'id' বা 'name'
        } else {
            $products = $products->orderBy('created_at', 'desc');
        }


        $products = $products->paginate(15); // Pagination যোগ করা হলো

        return view('backend.wholesale.products.index', compact('products', 'search', 'sort_by'));
    }

}
