<?php

namespace App\Support;

use App\Mail\LeadReceived;
use App\Models\Lead;
use App\Models\Property;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifiesNewLead
{
    public static function send(?Property $property, Lead $lead): void
    {
        try {
            $agentEmail = is_string($property?->user?->email) ? $property->user->email : null;
            $adminEmail = (string) config('app.admin_email', 'admin@rumah123.local');

            $mailable = new LeadReceived($property, $lead);

            if (is_string($agentEmail) && $agentEmail !== '') {
                $pending = Mail::to($agentEmail);

                if ($adminEmail !== '' && $adminEmail !== $agentEmail) {
                    $pending->bcc($adminEmail);
                }

                $pending->send($mailable);

                return;
            }

            if ($adminEmail !== '') {
                Mail::to($adminEmail)->send($mailable);
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim notifikasi lead baru.', [
                'lead_id' => $lead->id ?? null,
                'property_id' => $property?->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
