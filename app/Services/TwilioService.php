<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected Client $client;
    protected string $from;

    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
        $this->from   = env('TWILIO_FROM');
    }

    public function sendWhatsApp(string $to, string $message): void
    {
        $phone = 'whatsapp:+' . preg_replace('/\D/', '', $to);

        $this->client->messages->create($phone, [
            'from' => $this->from,
            'body' => $message,
        ]);
    }

    public function sendTemplate(string $to, string $contentSid, array $variables): void
    {
        $phone = 'whatsapp:+' . preg_replace('/\D/', '', $to);

        $this->client->messages->create($phone, [
            'from'             => $this->from,
            'contentSid'       => $contentSid,
            'contentVariables' => json_encode($variables),
        ]);
    }
}
