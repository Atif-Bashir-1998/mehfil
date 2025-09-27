<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HateSpeech implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $url = config('services.hate_speech_api.url') . ':' . config('services.hate_speech_api.port');
            // Make API call to hate speech detection service
            $response = Http::timeout(10)
                ->post($url . '/predict', [
                    'text' => $value
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['isHateSpeech']) && $data['isHateSpeech'] === true) {
                    $confidence = $data['confidence'] ?? 'high';
                    $fail("Your content contains offensive language (confidence: {$confidence}). Please revise your post.");
                    return;
                }

                // Text is clean, validation passes
                return;
            }

            Log::info(json_encode($response));

            // If API is down, log warning but allow the post (fail-open)
            Log::warning('Hate speech API unavailable, skipping validation for: ' . substr($value, 0, 100));

        } catch (\Exception $e) {
            Log::error('Hate speech validation error: ' . $e->getMessage());
            // Fail-open: if service is down, allow the post to maintain availability
        }
    }
}
