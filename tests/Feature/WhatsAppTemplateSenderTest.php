<?php namespace Tests\Feature;

use App\Services\Notifications\WhatsAppTemplateSender;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppTemplateSenderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.notifications.provider_enabled', true);
        config()->set('services.whatsapp.access_token', 'test-token');
        config()->set('services.whatsapp.phone_number_id', '123456');
        config()->set('services.whatsapp.api_version', 'v25.0');
        config()->set('services.whatsapp.template_language', 'ar');

        Http::preventStrayRequests();
    }

    public function test_it_sends_a_whatsapp_template(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(
                [
                    'messages' => [['id' => 'wamid.template123']],
                ],
                200,
            ),
        ]);

        $result = app(WhatsAppTemplateSender::class)->send('967777123456', 'client_hearing_reminder', ['محمد أحمد', '15/11/2026']);

        $this->assertSame('accepted', $result->status);
        $this->assertSame('wamid.template123', $result->providerMessageId);

        Http::assertSent(function ($request) {
            return $request['type'] === 'template' && $request['template']['name'] === 'client_hearing_reminder' && $request['template']['language']['code'] === 'ar' && $request['template']['components'][0]['parameters'][0]['text'] === 'محمد أحمد' && $request['template']['components'][0]['parameters'][1]['text'] === '15/11/2026';
        });
    }

    public function test_it_does_not_send_when_disabled(): void
    {
        Http::fake();

        config()->set('services.notifications.provider_enabled', false);

        $result = app(WhatsAppTemplateSender::class)->send('967777123456', 'client_hearing_reminder', ['محمد أحمد', '15/11/2026']);

        $this->assertSame('failed', $result->status);
        $this->assertSame('provider_disabled', $result->failureCode);

        Http::assertNothingSent();
    }

    public function test_server_error_is_unknown(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response([], 500),
        ]);

        $result = app(WhatsAppTemplateSender::class)->send('967777123456', 'client_hearing_reminder', ['محمد أحمد', '15/11/2026']);

        $this->assertSame('unknown', $result->status);
        $this->assertSame('meta_server_error', $result->failureCode);
    }
}
