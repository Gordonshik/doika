<?php

namespace Diglabby\Doika\Http\Controllers\Webhooks\PaymentGateways;

use App\Http\Controllers\Controller;
use Diglabby\Doika\Models\Donator;
use Diglabby\Doika\Models\Transaction;
use Illuminate\Http\Request;

/**
 * @see https://docs.bepaid.by/ru/webhooks
 */
final class BePaidWebhookHandler extends Controller
{
    public function donated(Request $request, int $campaignId)
    {
        \Log::debug('bePaid donated webhook', $request->all());

        /** @var Donator $donator */
        $donator = Donator::query()->firstOrCreate(['email' => $request->json('transaction.customer.email')]);

        Transaction::query()->create([
            'campaign_id' => $campaignId,
            'donator_id' => $donator->id,
            'payment_gateway' => 'bePaid',
            'payment_gateway_transaction_id' => $request->json('transaction.uid'),
            'amount' => $request->json('transaction.amount'),
            'currency' => $request->json('transaction.currency'),
            'status' => Transaction::STATUS_SUCCESSFUL,
            'status_message' => $request->json('transaction.payment.message'),
        ]);
    }
}
