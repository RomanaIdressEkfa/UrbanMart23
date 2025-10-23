<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Product;
use App\Notifications\PreorderProductArrivedNotification;
use Illuminate\Support\Facades\DB;

class PreorderController extends Controller
{
    /**
     * Display a listing of pre-orders.
     * Mohammad Hassan
     */
    public function index(Request $request)
    {
        $query = Order::where('is_preorder', 1)
                     ->with(['user', 'orderDetails.product'])
                     ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('preorder_status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%")
                               ->orWhere('phone', 'LIKE', "%{$search}%");
                  });
            });
        }

        $preorders = $query->paginate(15);

        return view('backend.preorders.index', compact('preorders'));
    }

    /**
     * Show the specified pre-order.
     * Mohammad Hassan
     */
    public function show($id)
    {
        $preorder = Order::where('is_preorder', 1)
                         ->with(['user', 'orderDetails.product', 'orderDetails.variation'])
                         ->findOrFail($id);

        return view('backend.preorders.show', compact('preorder'));
    }

    /**
     * Mark product as arrived for a pre-order.
     * Mohammad Hassan
     */
    public function markArrived(Request $request)
    {
        try {
            $preorder = Order::where('is_preorder', 1)->findOrFail($request->id);
            
            // Update status to product_arrived
            $preorder->preorder_status = 'product_arrived';
            $preorder->product_arrived_at = now();
            $preorder->save();

            // Notify customer
            if ($preorder->user) {
                $preorder->user->notify(new PreorderProductArrivedNotification($preorder));
            }

            return response()->json([
                'success' => true,
                'message' => translate('Product marked as arrived and customer notified successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('Something went wrong: ') . $e->getMessage()
            ]);
        }
    }

    /**
     * Notify customer about pre-order status.
     * Mohammad Hassan
     */
    public function notifyCustomer(Request $request)
    {
        try {
            $preorder = Order::where('is_preorder', 1)->findOrFail($request->id);
            
            if ($preorder->user) {
                $preorder->user->notify(new PreorderProductArrivedNotification($preorder));
                
                return response()->json([
                    'success' => true,
                    'message' => translate('Customer notified successfully')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => translate('Customer not found')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('Something went wrong: ') . $e->getMessage()
            ]);
        }
    }

    /**
     * Update pre-order status.
     * Mohammad Hassan
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,product_arrived,completed,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $preorder = Order::where('is_preorder', 1)->findOrFail($id);
            
            $preorder->preorder_status = $request->status;
            
            if ($request->filled('notes')) {
                $preorder->preorder_notes = $request->notes;
            }

            // Set timestamps based on status
            switch ($request->status) {
                case 'confirmed':
                    $preorder->confirmed_at = now();
                    break;
                case 'product_arrived':
                    $preorder->product_arrived_at = now();
                    // Notify customer
                    if ($preorder->user) {
                        $preorder->user->notify(new PreorderProductArrivedNotification($preorder));
                    }
                    break;
                case 'completed':
                    $preorder->completed_at = now();
                    break;
                case 'cancelled':
                    $preorder->cancelled_at = now();
                    break;
            }

            $preorder->save();

            flash(translate('Pre-order status updated successfully'))->success();
            return redirect()->back();

        } catch (\Exception $e) {
            flash(translate('Something went wrong: ') . $e->getMessage())->error();
            return redirect()->back();
        }
    }

    /**
     * Bulk mark products as arrived.
     * Mohammad Hassan
     */
    public function bulkMarkArrived(Request $request)
    {
        try {
            $ids = $request->ids;
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => translate('No pre-orders selected')
                ]);
            }

            $preorders = Order::where('is_preorder', 1)
                             ->whereIn('id', $ids)
                             ->with('user')
                             ->get();

            foreach ($preorders as $preorder) {
                $preorder->preorder_status = 'product_arrived';
                $preorder->product_arrived_at = now();
                $preorder->save();

                // Notify customer
                if ($preorder->user) {
                    $preorder->user->notify(new PreorderProductArrivedNotification($preorder));
                }
            }

            return response()->json([
                'success' => true,
                'message' => translate('Products marked as arrived and customers notified successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('Something went wrong: ') . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk notify customers.
     * Mohammad Hassan
     */
    public function bulkNotifyCustomers(Request $request)
    {
        try {
            $ids = $request->ids;
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => translate('No pre-orders selected')
                ]);
            }

            $preorders = Order::where('is_preorder', 1)
                             ->whereIn('id', $ids)
                             ->with('user')
                             ->get();

            $notified = 0;
            foreach ($preorders as $preorder) {
                if ($preorder->user) {
                    $preorder->user->notify(new PreorderProductArrivedNotification($preorder));
                    $notified++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => translate('Customers notified successfully') . " ({$notified} " . translate('notifications sent') . ")"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('Something went wrong: ') . $e->getMessage()
            ]);
        }
    }

    /**
     * Get pre-order statistics for dashboard.
     * Mohammad Hassan
     */
    public function getStats()
    {
        $stats = [
            'total' => Order::where('is_preorder', 1)->count(),
            'pending' => Order::where('is_preorder', 1)->where('preorder_status', 'pending')->count(),
            'confirmed' => Order::where('is_preorder', 1)->where('preorder_status', 'confirmed')->count(),
            'arrived' => Order::where('is_preorder', 1)->where('preorder_status', 'product_arrived')->count(),
            'completed' => Order::where('is_preorder', 1)->where('preorder_status', 'completed')->count(),
            'cancelled' => Order::where('is_preorder', 1)->where('preorder_status', 'cancelled')->count(),
        ];

        return response()->json($stats);
    }
}