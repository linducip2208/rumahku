<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;

class TrackController extends Controller
{
    public function click(Property $property, string $channel): RedirectResponse
    {
        abort_unless(in_array($channel, ['wa', 'tel'], true), 404);

        $rawPhone = optional(optional($property->user)->agentProfile)->phone
            ?? optional(optional($property->user)->agentProfile)->wa
            ?? null;
        $number = $rawPhone ? preg_replace('/\D+/', '', (string) $rawPhone) : null;
        if ($number && str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        if (! $number) {
            return redirect()->route('properties.show', $property->slug);
        }

        $property->increment($channel === 'wa' ? 'wa_clicks' : 'tel_clicks');

        if ($channel === 'wa') {
            $text = urlencode('Halo, saya tertarik dengan properti: '.$property->title);

            return redirect()->away('https://wa.me/'.$number.'?text='.$text);
        }

        return redirect()->away('tel:'.$number);
    }
}
