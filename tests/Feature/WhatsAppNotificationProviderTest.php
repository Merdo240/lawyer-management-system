<?php namespace Tests\Feature;

use App\Services\Notifications\WhatsAppNotificationProvider;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppNotificationProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.notifications.provider_enabled', true);
        config()->set('services.whatsapp.access_token', 'fake-test-token');
        config()->set('services.whatsapp.phone_number_id', '123456789');
        config()->set('services.whatsapp.api_version', 'v25.0');

        Http::preventStrayRequests();
    }

    public function test_it_does_not_send_when_provider_is_disabled(): void
    {
        Http::fake();

        config()->set('services.notifications.provider_enabled', false);

        $result = app(WhatsAppNotificationProvider::class)->send('967777123456', 'Test message');

        $this->assertSame('failed', $result->status);
        $this->assertSame('provider_disabled', $result->failureCode);

        Http::assertNothingSent();
    }

    public function test_it_does_not_send_when_configuration_is_missing(): void
    {
        Http::fake();

        config()->set('services.whatsapp.access_token', null);

        $result = app(WhatsAppNotificationProvider::class)->send('967777123456', 'Test message');

        $this->assertSame('failed', $result->status);
        $this->assertSame('provider_not_configured', $result->failureCode);

        Http::assertNothingSent();
    }

    public function test_it_accepts_a_successful_whatsapp_request(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(
                [
                    'messages' => [['id' => 'wamid.test123']],
                ],
                200,
            ),
        ]);

        $result = app(WhatsAppNotificationProvider::class)->send('967777123456', 'Test message');

        $this->assertSame('accepted', $result->status);
        $this->assertSame('wamid.test123', $result->providerMessageId);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' && $request->url() === 'https://graph.facebook.com/v25.0/123456789/messages' && $request->hasHeader('Authorization', 'Bearer fake-test-token') && $request['messaging_product'] === 'whatsapp' && $request['to'] === '967777123456' && $request['type'] === 'text' && $request['text']['body'] === 'Test message';
        });

        Http::assertSentCount(1);
    }

    public function test_it_handles_whatsapp_api_rejection(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(
                [
                    'error' => [
                        'code' => 190,
                        'message' => 'Invalid OAuth access token.',
                    ],
                ],
                401,
            ),
        ]);

        $result = app(WhatsAppNotificationProvider::class)->send('967777123456', 'Test message');

        $this->assertSame('failed', $result->status);
        $this->assertSame('meta_api_rejected', $result->failureCode);
        $this->assertNull($result->providerMessageId);

        Http::assertSentCount(1);
    }

    public function test_it_handles_missing_message_id(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(
                [
                    'messages' => [],
                ],
                200,
            ),
        ]);

        $result = app(WhatsAppNotificationProvider::class)->send('967777123456', 'Test message');

        $this->assertSame('unknown', $result->status);
        $this->assertSame('missing_message_id', $result->failureCode);
        $this->assertNull($result->providerMessageId);
    }

    public function test_it_handles_connection_failure(): void
    {
        Http::fake(function () {
            throw new \RuntimeException('Simulated connection failure.');
        });

        $result = app(WhatsAppNotificationProvider::class)->send('967777123456', 'Test message');

        $this->assertSame('unknown', $result->status);
        $this->assertSame('request_outcome_unknown', $result->failureCode);
    }

    
public function test_it_marks_meta_server_error_as_unknown(): void
{
    Http::fake([
        'graph.facebook.com/*' => Http::response([
            'error' => [
                'code' => 131000,
                'message' => 'Internal server error.',
            ],
        ], 500),
    ]);

    $result = app(WhatsAppNotificationProvider::class)
        ->send('967777123456', 'Test message');

    $this->assertSame('unknown', $result->status);
    $this->assertSame('meta_server_error', $result->failureCode);

    Http::assertSentCount(1);
}

}
