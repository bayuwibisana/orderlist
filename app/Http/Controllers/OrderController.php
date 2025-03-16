<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        // Ambil user_id dari token pengguna yang sedang login
        $user_id = $request->user()->id;
        $today = Carbon::today();

        // Ambil data order berdasarkan user_id
        $orders = Order::where('user_id', $user_id)
            ->orderBy('position')
            ->whereDate('created_at', $today)
            ->orderBy('id')->get();

        // Response JSON
        return response()->json([
            'message' => 'Orders retrieved successfully',
            'data' => $orders,
        ]);
    }

    // Method untuk menyimpan data order
    public function store(Request $request)
    {

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'product' => 'nullable|string|max:255',
            'position' => 'nullable|integer',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user_id = $request->user()->id;

        // Simpan data ke tabel order
        $order = Order::create([
            'user_id' => $user_id,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'product' => $request->product,
            'position' => $request->position,
        ]);

        // Response JSON
        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePosition(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array', // Pastikan 'items' adalah array
            'items.*.id' => 'required|integer', // Validasi 'id' di setiap item
            'items.*.position' => 'required|integer', // Validasi 'position' di setiap item
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user_id = $request->user()->id;

        // Jika validasi berhasil
        $items = $request->items;

        // Lakukan sesuatu dengan data yang valid, misalnya update database
        foreach ($items as $item) {
            // Contoh: Update posisi item di database
            Order::where('id', $item['id'])->where('user_id', $user_id)->update(['position' => $item['position']]);
        }

        // Response JSON
        return response()->json([
            'message' => 'Positions updated successfully',
            'data' => $items,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|max:100',
            'nama' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
            'product' => 'required|string|max:255',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user_id = $request->user()->id;


        Order::where('id', $request->id)
            ->where('user_id', $user_id)
            ->update(
                [
                    'nama' => $request->nama,
                    'alamat' => $request->alamat,
                    'product' => $request->product,
                ]
            );


        // Response JSON
        return response()->json([
            'message' => 'Data updated successfully',
        ]);
    }

    public function destroy(Request $request, $id)
    {

        $user_id = $request->user()->id;

        // Hapus data hanya jika id dan user_id sesuai
        $deleted = Order::where('id', $id)
            ->where('user_id', $user_id)
            ->delete();

        // Periksa apakah data berhasil dihapus
        if ($deleted) {
            return \response()->json(['message' => 'Item deleted successfully']);
        }

        // Jika data tidak ditemukan atau tidak milik user
        return \response()->json(['message' => 'Item not found or unauthorized'], 404);
    }
}
