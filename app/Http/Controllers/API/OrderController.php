<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $orderModel;

    public function __construct(Order $order)
    {
        $this->orderModel = $order;
    }

    public function createOrder(CreateOrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $order = $this->orderModel->createOrder($request);
            DB::commit();
            return response()->json(['order' => $order], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Order creation failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function getListOfOrders(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $orders = $this->orderModel->paginate($limit);
            $orderCollection = new OrderCollection($orders);
            return response()->json($orderCollection, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get list of orders failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getOrderDetails($id)
    {
        try {
            $order = $this->orderModel->getOrderById($id);
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }
            $orderResource = new OrderResource($order);
            return response()->json(['order' => $orderResource], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get order detail failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getFilterOrders(Request $request)
    {
        try {
            $filters = $request->all();
            $orders = $this->orderModel->filterOrders($filters);
            return response()->json($orders, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get filtered order failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function storeOrderInCache(Request $request)
    {
        try {
            $order = $this->orderModel->storeOrderInCache($request);
            return response()->json(['order' => $order], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Create order cache failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function getOrderFromCacheByToken($token)
    {
        try {
            $orderData = Cache::get('order_' . $token);

            if ($orderData) {
                return response()->json(['order' => json_decode($orderData)], 200);
            } else {
                return response()->json(['message' => 'Order cache not found or expired'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get order cache detail failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteOrderFromCacheByToken($token)
    {
        try {
            $cacheKey = 'order_' . $token;

            if (Cache::has($cacheKey)) {
                Cache::forget($cacheKey);
                return response()->json(['message' => 'Order deleted from cache successfully'], 200);
            } else {
                return response()->json(['message' => 'Order not found or already deleted'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete order from cache', 'error' => $e->getMessage()], 500);
        }
    }

    public function viewSessionData()
    {
        // Retrieve all session data
        $sessionData = session()->all();

        // Return the session data as a JSON response
        return response()->json(['session_data' => $sessionData]);
    }
}
