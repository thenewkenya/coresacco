<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class ReceiptQrController extends Controller
{
    public function show(Transaction $transaction): Response
    {
        $receiptUrl = route('transactions.receipt', $transaction);
        $qrApi = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($receiptUrl);

        $qrResponse = Http::timeout(5)->get($qrApi);
        if (!$qrResponse->ok()) {
            return response('QR generation failed', 502);
        }

        return response($qrResponse->body(), 200, [
            'Content-Type' => $qrResponse->header('Content-Type', 'image/png'),
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}


