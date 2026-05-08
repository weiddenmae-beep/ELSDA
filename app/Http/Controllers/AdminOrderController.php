<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    private function checkAdmin(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }
    }

    public function index()
    {
        $this->checkAdmin();

        $orders = Order::with('items.product')
            ->latest()
            ->get();

        return view('admin.orders', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->checkAdmin();

        $data = $request->validate([
            'status' => 'required|string|in:confirmed,completed',
        ]);

        if ($data['status'] === 'confirmed' && $order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be confirmed.',
            ], 422);
        }

        if ($data['status'] === 'completed' && $order->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Only confirmed orders can be completed.',
            ], 422);
        }

        $payload = ['status' => $data['status']];
        if ($data['status'] === 'confirmed') {
            $payload['confirmed_at'] = now();
        }
        if ($data['status'] === 'completed') {
            $payload['completed_at'] = now();
        }

        $order->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
        ]);
    }

    public function cancel(Request $request, Order $order)
    {
        $this->checkAdmin();

        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!in_array($order->status, ['pending', 'confirmed'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or confirmed orders can be cancelled.',
            ], 422);
        }

        $order->update([
            'status' => 'cancelled',
            'cancel_reason' => $data['reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.',
        ]);
    }

    public function history()
    {
        $this->checkAdmin();

        $orders = Order::with('items.product')
            ->whereIn('status', ['completed', 'cancelled'])
            ->latest('updated_at')
            ->get();

        return view('admin.history', compact('orders'));
    }
}
