<?php

namespace App\Http\Controllers;

use App\Models\PropertyOrder;
use App\Services\Payments\MissingCredentials;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Alur pembayaran order paket.
 *
 * Rute didaftarkan oleh main agent (file ini hanya controller baru).
 * Snippet rute yang disarankan — taruh di routes/web.php:
 *
 *   use App\Http\Controllers\PayController;
 *
 *   Route::middleware('auth')->group(function () {
 *       Route::get('/pay/{order}', [PayController::class, 'pay'])->name('pay.show');
 *   });
 *   Route::post('/pay/callback/{driver}', [PayController::class, 'callback'])->name('pay.callback');
 *
 * Catatan: route callback HARUS di luar middleware auth karena dipanggil
 * server-to-server oleh Midtrans/Xendit.
 */
class PayController extends Controller
{
    public function pay(Request $request, PropertyOrder $order, PaymentManager $manager): mixed
    {
        if ((int) $order->user_id !== (int) $request->user()->id) {
            abort(Response::HTTP_FORBIDDEN, 'Order ini bukan milik Anda.');
        }

        if ($order->status === 'paid') {
            return redirect()->route('agent.orders.index')->with('success', 'Order sudah lunas.');
        }

        $gateway = $manager->driver();
        $charge = $gateway->createCharge($order);

        $order->forceFill([
            'payment_reference' => $charge['reference_id'],
            'payment_driver' => $gateway->name(),
        ])->save();

        if (! empty($charge['pay_url'])) {
            return redirect()->away($charge['pay_url']);
        }

        return view('pay.instructions', [
            'order' => $order->fresh(),
            'charge' => $charge,
        ]);
    }

    public function callback(Request $request, string $driver, PaymentManager $manager): mixed
    {
        try {
            $gateway = $manager->driver($driver);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['ok' => false, 'error' => 'unknown_driver'], 200);
        }

        $payload = array_merge($request->all(), [
            'callback_token' => (string) $request->header('X-CALLBACK-TOKEN', ''),
        ]);

        try {
            $order = $gateway->handleCallback($payload);
        } catch (MissingCredentials $e) {
            return response()->json(['ok' => false, 'error' => 'missing_credentials'], 200);
        }

        return response()->json([
            'ok' => true,
            'order_id' => $order?->id,
            'status' => $order?->status,
        ]);
    }
}
