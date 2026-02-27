<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Membership;
use Illuminate\Support\Facades\Log;
use Midtrans\Notification as MidtransNotification;
use Midtrans\Config;

class MidtransController extends Controller
{
    /**
     * Endpoint yang dipanggil oleh Midtrans untuk memberitahu perubahan status transaksi.
     */
    public function notification(Request $request)
    {
        // konfigurasi midtrans (sama seperti di controller lain)
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        // log for debug
        Log::info('midtrans config in notification', config('midtrans'));
        Log::info('midtrans Config static in notification', [
            'serverKey' => Config::$serverKey,
            'clientKey' => Config::$clientKey
        ]);

        // notify class handles parsing signature/key validation
        $notif = new MidtransNotification();
        $orderId = $notif->order_id;

        // Handle based on Order ID prefix
        if (str_starts_with($orderId, 'SRV-')) {
            // Case for ServiceTransaction
            $parts = explode('-', $orderId);
            $serviceTransactionId = $parts[1];
            $serviceTransaction = \App\Models\ServiceTransaction::find($serviceTransactionId);

            if (!$serviceTransaction) {
                Log::warning('Midtrans notification received for unknown service transaction: ' . $orderId);
                return response('Service transaction not found', 404);
            }

            $currentStatus = $notif->transaction_status;
            switch ($currentStatus) {
                case 'capture':
                case 'settlement':
                    $serviceTransaction->status = 'scheduled';
                    break;
                case 'pending':
                    $serviceTransaction->status = 'pending';
                    break;
                case 'deny':
                case 'cancel':
                case 'expire':
                    $serviceTransaction->status = 'cancelled';
                    break;
            }
            $serviceTransaction->save();

            // Link trainer-member if scheduled
            if ($serviceTransaction->status === 'scheduled' && $serviceTransaction->trainer_id) {
                \App\Models\TrainerMember::firstOrCreate([
                    'trainer_id' => $serviceTransaction->trainer_id,
                    'member_id' => $serviceTransaction->user_id,
                ], [
                    'assigned_at' => now(),
                    'status' => 'active'
                ]);
            }

            return response('OK', 200);
        }

        // Handle based on Order ID prefix for Products
        if (str_starts_with($orderId, 'PRD-')) {
            $parts = explode('-', $orderId);
            $transactionId = $parts[1];
            $transaction = \App\Models\Transaction::find($transactionId);

            if (!$transaction) {
                Log::warning('Midtrans notification received for unknown product transaction: ' . $orderId);
                return response('Product transaction not found', 404);
            }

            $currentStatus = $notif->transaction_status;
            switch ($currentStatus) {
                case 'capture':
                case 'settlement':
                    $transaction->status = 'validated';
                    break;
                case 'pending':
                    $transaction->status = 'pending';
                    break;
                case 'deny':
                case 'cancel':
                case 'expire':
                    $transaction->status = 'cancelled';
                    break;
            }
            $transaction->save();

            return response('OK', 200);
        }

        // Case for standard Transactions (Membership)
        $transaction = Transaction::where('invoice_id', $orderId)->first();

        if (!$transaction) {
            Log::warning('Midtrans notification received for unknown order: ' . $orderId);
            return response('Order not found', 404);
        }

        // map midtrans status to our status column
        $status = $notif->transaction_status;
        $fraud = $notif->fraud_status ?? null;

        switch ($status) {
            case 'capture':
                if ($fraud === 'challenge') {
                    $transaction->status = 'pending';
                } else {
                    $transaction->status = 'validated';
                }
                break;
            case 'settlement':
                $transaction->status = 'validated';
                break;
            case 'pending':
                $transaction->status = 'pending';
                break;
            case 'deny':
            case 'cancel':
            case 'expire':
                $transaction->status = 'cancelled';
                break;
        }
        $transaction->save();

        // jika transaksi sudah divalidated aktifkan membership yang berkaitan
        if ($transaction->status === 'validated') {
            $membership = Membership::where('transaction_id', $transaction->id)->first();
            if ($membership && $membership->status !== 'active') {
                $membership->status = 'active';
                $membership->save();
            }
        }

        return response('OK', 200);
    }
}
