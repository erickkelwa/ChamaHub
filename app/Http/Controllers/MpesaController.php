<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Payment;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    protected MpesaService $mpesa;

    public function __construct(MpesaService $mpesa)
    {
        $this->mpesa = $mpesa;
    }

    /**
     * Initiate STK push for a contribution payment.
     */
    public function initiatePush(Request $request)
    {
        $request->validate([
            'contribution_id' => 'required|exists:contributions,id',
            'phone'           => 'required|string',
        ]);

        $contribution = Contribution::findOrFail($request->contribution_id);

        $amountDue = $contribution->amount_due - $contribution->amount_paid;

        if ($amountDue <= 0) {
            return back()->with('error', 'This contribution is already fully paid.');
        }

        $phone = preg_replace('/^0/', '254', $request->phone); // Normalize to 2547XXXXXXXX

        $result = $this->mpesa->stkPush(
            $phone,
            $amountDue,
            'ChamaHub-' . $contribution->id,
            'Contribution for ' . $contribution->month
        );

        if ($result['success']) {
            // Store a pending payment record
            Payment::create([
                'user_id'         => auth()->id() ?? $contribution->user_id,
                'payable_type'    => Contribution::class,
                'payable_id'      => $contribution->id,
                'amount'          => $amountDue,
                'payment_method'  => 'mpesa',
                'status'          => 'pending',
                'mpesa_reference' => $result['data']['CheckoutRequestID'] ?? null,
                'phone_number'    => $phone,
            ]);

            return back()->with('success', 'STK Push sent to ' . $phone . '. Enter your M-Pesa PIN on your phone to complete.');
        }

        return back()->with('error', 'M-Pesa request failed: ' . $result['message']);
    }

    /**
     * Initiate custom deposit/savings payment via STK push.
     */
    public function initiateDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'phone'  => 'required|string',
        ]);

        $user = auth()->user();
        $amount = (float) $request->amount;
        $phone = preg_replace('/^0/', '254', $request->phone);

        $monthLabel = 'Deposit - ' . date('M Y'); // 18 chars (max 20)

        // Create a contribution record for this ad-hoc deposit
        $contribution = Contribution::create([
            'user_id'     => $user->id,
            'month'       => $monthLabel,
            'amount_due'  => $amount,
            'amount_paid' => 0,
            'status'      => 'unpaid',
        ]);

        $result = $this->mpesa->stkPush(
            $phone,
            $amount,
            'ChamaSave-' . $contribution->id,
            'Deposit for ' . $user->name
        );

        if ($result['success']) {
            Payment::create([
                'user_id'         => $user->id,
                'payable_type'    => Contribution::class,
                'payable_id'      => $contribution->id,
                'amount'          => $amount,
                'payment_method'  => 'mpesa',
                'status'          => 'pending',
                'mpesa_reference' => $result['data']['CheckoutRequestID'] ?? null,
                'phone_number'    => $phone,
            ]);

            return back()->with('success', 'STK Push of Ksh ' . number_format($amount, 2) . ' sent to ' . $phone . '. Check your phone to complete!');
        }

        return back()->with('error', 'M-Pesa request failed: ' . $result['message']);
    }

    /**
     * Handle the M-Pesa callback (called by Safaricom servers).
     */
    public function callback(Request $request)
    {
        Log::info('M-Pesa Callback received', $request->all());

        $parsed = $this->mpesa->parseCallback($request->all());

        if (!$parsed['success']) {
            Log::warning('M-Pesa callback: transaction failed', $parsed);
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        // Find the pending payment by mpesa_reference
        $payment = Payment::where('mpesa_reference', $parsed['checkout_id'])->first();

        if ($payment) {
            $payment->update([
                'status'          => 'completed',
                'mpesa_reference' => $parsed['mpesa_code'] ?? $payment->mpesa_reference,
                'paid_at'         => now(),
            ]);

            // Update contribution record
            $contribution = $payment->payable;
            if ($contribution) {
                $newPaid = $contribution->amount_paid + $parsed['amount'];
                $contribution->update([
                    'amount_paid' => $newPaid,
                    'status'      => $newPaid >= $contribution->amount_due ? 'paid' : 'partial',
                    'paid_at'     => now(),
                ]);
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}
