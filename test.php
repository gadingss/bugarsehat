<?php
$transaction = \App\Models\ServiceTransaction::where('user_id', 5)->where('service_id', 3)->first();
dump($transaction->id);
$availableSession = \App\Models\ServiceSession::where('service_transaction_id', $transaction->id)
    ->where('status', 'pending')
    ->whereNull('scheduled_date')
    ->first();
dump($availableSession ? 'FOUND' : 'NULL');
