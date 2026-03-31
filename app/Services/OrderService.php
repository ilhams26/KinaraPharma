<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Batch;
use App\Models\PergerakanStok;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function generateOrderCode(): string
    {
        return 'ORD-' . strtoupper(Str::random(8));
    }

    // ALGORITMA FEFO (First Expired First Out)
    public function processFEFO(int $obatId, int $qtyNeeded): void
    {
        $batches = Batch::where('obat_id', $obatId)
            ->where('jumlah_sisa', '>', 0)
            ->orderBy('expired_date', 'asc') // Urutkan dari yang paling cepat kadaluarsa
            ->get();

        $qtyRemaining = $qtyNeeded;

        foreach ($batches as $batch) {
            if ($qtyRemaining <= 0) break;

            $qtyFromBatch = min($qtyRemaining, $batch->jumlah_sisa);

            $batch->jumlah_sisa -= $qtyFromBatch;
            $batch->save();

            PergerakanStok::create([
                'obat_id' => $obatId,
                'tipe' => 'keluar',
                'jumlah' => $qtyFromBatch,
                'sumber' => 'pesanan',
            ]);

            $qtyRemaining -= $qtyFromBatch;
        }
    }

    public function createOrder($userId, array $items, string $paymentMethod): Order
    {
        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($items as $item) {
                $total += $item['harga'] * $item['qty'];
            }

            $order = Order::create([
                'user_id' => $userId,
                'order_code' => $this->generateOrderCode(),
                'metode_pembayaran' => $paymentMethod,
                'total_harga' => $total,
                'status' => 'diproses',
                'payment_status' => $paymentMethod === 'cash' ? 'paid' : 'unpaid',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'obat_id' => $item['obat_id'],
                    'harga' => $item['harga'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['harga'] * $item['qty'],
                ]);

                // Panggil algoritma pemotongan stok otomatis
                $this->processFEFO($item['obat_id'], $item['qty']);
            }

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
