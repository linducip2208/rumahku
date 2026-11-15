<?php

namespace Tests\Feature;

use App\Http\Controllers\PayController;
use App\Models\Package;
use App\Models\Property;
use App\Models\PropertyOrder;
use App\Models\Setting;
use App\Models\User;
use App\Services\Payments\ManualDriver;
use App\Services\Payments\MidtransDriver;
use App\Services\Payments\MissingCredentials;
use App\Services\Payments\PaymentManager;
use App\Services\Payments\XenditDriver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    /**
     * @return array{user: User, property: Property, order: PropertyOrder}
     */
    private function makeOrder(bool $featuredPackage = true): array
    {
        /** @var Package $package */
        $package = Package::factory()->create([
            'slug' => 'pro-test',
            'price' => 499000,
            'duration_days' => 30,
            'featured_included' => $featuredPackage,
        ]);

        /** @var User $user */
        $user = User::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->for($user)->create(['featured' => false]);

        /** @var PropertyOrder $order */
        $order = PropertyOrder::factory()->create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'package_slug' => $package->slug,
            'amount' => $package->price,
            'status' => 'pending',
        ]);

        return compact('user', 'property', 'order');
    }

    public function test_manual_driver_creates_charge_without_credentials(): void
    {
        ['order' => $order] = $this->makeOrder();

        Setting::set('payments.bank_accounts', 'BCA 1234567890 a.n. PT Rumah123', 'payments');

        $charge = (new ManualDriver)->createCharge($order);

        $this->assertStringStartsWith('MANUAL-', $charge['reference_id']);
        $this->assertNull($charge['pay_url']);
        $this->assertNull($charge['token']);
        $this->assertStringContainsString('499.000', $charge['raw']['instructions']);
        $this->assertStringContainsString('BCA 1234567890', $charge['raw']['bank_accounts']);
    }

    public function test_midtrans_driver_without_key_throws_missing_credentials(): void
    {
        ['order' => $order] = $this->makeOrder();

        $this->expectException(MissingCredentials::class);
        $this->expectExceptionMessageMatches('/midtrans_server_key/');

        (new MidtransDriver)->createCharge($order);
    }

    public function test_xendit_driver_without_key_throws_missing_credentials(): void
    {
        ['order' => $order] = $this->makeOrder();

        $this->expectException(MissingCredentials::class);
        $this->expectExceptionMessageMatches('/xendit_secret_key/');

        (new XenditDriver)->createCharge($order);
    }

    public function test_midtrans_driver_with_fake_http_returns_pay_url(): void
    {
        ['order' => $order] = $this->makeOrder();

        Setting::set('payments.midtrans_server_key', 'fake-server-key', 'payments');

        $fake = fn (string $url, array $headers, array $body): array => [
            'token' => 'snap-token-123',
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-123',
        ];

        $charge = (new MidtransDriver($fake))->createCharge($order->load('user'));

        $this->assertStringStartsWith('MID-', $charge['reference_id']);
        $this->assertSame('snap-token-123', $charge['token']);
        $this->assertStringStartsWith('https://', (string) $charge['pay_url']);
    }

    public function test_midtrans_callback_with_valid_signature_marks_paid(): void
    {
        ['property' => $property, 'order' => $order] = $this->makeOrder();

        Setting::set('payments.midtrans_server_key', 'test-server-key', 'payments');

        $reference = 'MID-'.$order->id.'-20261115010101';
        $order->forceFill(['payment_reference' => $reference])->save();

        $signature = hash('sha512', $reference.'200'.'499000'.'test-server-key');

        $result = (new MidtransDriver)->handleCallback([
            'order_id' => $reference,
            'status_code' => '200',
            'gross_amount' => '499000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
        ]);

        $this->assertNotNull($result);
        $this->assertSame('paid', $order->fresh()->status);
        $this->assertTrue($property->fresh()->featured);
    }

    public function test_midtrans_callback_with_invalid_signature_is_rejected(): void
    {
        ['order' => $order] = $this->makeOrder();

        Setting::set('payments.midtrans_server_key', 'test-server-key', 'payments');

        $reference = 'MID-'.$order->id.'-20261115010101';
        $order->forceFill(['payment_reference' => $reference])->save();

        $result = (new MidtransDriver)->handleCallback([
            'order_id' => $reference,
            'status_code' => '200',
            'gross_amount' => '499000',
            'signature_key' => 'tampered-signature',
            'transaction_status' => 'settlement',
        ]);

        $this->assertNull($result);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_manual_callback_paid_marks_paid_and_featured(): void
    {
        ['property' => $property, 'order' => $order] = $this->makeOrder();

        $order->forceFill(['payment_reference' => 'MANUAL-'.$order->id.'-20261115'])->save();

        $result = (new ManualDriver)->handleCallback([
            'reference_id' => 'MANUAL-'.$order->id.'-20261115',
            'status' => 'paid',
        ]);

        $this->assertNotNull($result);
        $this->assertSame('paid', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->paid_at);
        $this->assertNotNull($order->fresh()->expires_at);
        $this->assertTrue($property->fresh()->featured);
    }

    public function test_manager_resolves_driver_from_setting(): void
    {
        Setting::set('payments.default_driver', 'manual', 'payments');

        $manager = new PaymentManager;

        $this->assertInstanceOf(ManualDriver::class, $manager->driver());
        $this->assertInstanceOf(MidtransDriver::class, $manager->driver('midtrans'));
        $this->assertInstanceOf(XenditDriver::class, $manager->driver('XENDIT'));

        $this->expectException(\InvalidArgumentException::class);

        $manager->driver('bitcoin');
    }

    public function test_pay_controller_manual_shows_instructions_view(): void
    {
        ['user' => $user, 'order' => $order] = $this->makeOrder();

        Setting::set('payments.default_driver', 'manual', 'payments');
        Setting::set('payments.bank_accounts', 'BCA 1234567890 a.n. PT Rumah123', 'payments');

        $request = Request::create('/pay/'.$order->id, 'GET');
        $request->setUserResolver(fn () => $user);

        $response = (new PayController)->pay($request, $order, new PaymentManager);

        $this->assertStringContainsString('MANUAL-', (string) $order->fresh()->payment_reference);
        $this->assertSame('manual', $order->fresh()->payment_driver);
        $this->assertSame('pay.instructions', $response->name());
    }

    public function test_pay_controller_forbids_other_users_order(): void
    {
        ['order' => $order] = $this->makeOrder();

        $intruder = User::factory()->create();

        $request = Request::create('/pay/'.$order->id, 'GET');
        $request->setUserResolver(fn () => $intruder);

        $this->expectException(HttpException::class);

        (new PayController)->pay($request, $order, new PaymentManager);
    }
}
