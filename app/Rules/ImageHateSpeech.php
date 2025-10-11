<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageHateSpeech implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $ocr_url = config('services.ocr_api.url') . ':' . config('services.ocr_api.port');
            $hate_speech_url = config('services.hate_speech_api.url') . ':' . config('services.hate_speech_api.port');

            // Step 1: Send image to OCR API
            $ocr_response = Http::timeout(30)
                ->attach('images', $value->get(), $value->getClientOriginalName())
                ->post($ocr_url . '/ocr');

            if (!$ocr_response->successful()) {
                Log::warning('OCR API unavailable, skipping image text detection');
                return; // Fail-open
            }

            $ocr_data = $ocr_response->json();

            // Step 2: Check each extracted text for hate speech
            if (isset($ocr_data['results'])) {
                foreach ($ocr_data['results'] as $result) {
                    if (isset($result['text']) && !empty(trim($result['text']))) {
                        $text = trim($result['text']);

                        // Step 3: Send extracted text to hate speech detector
                        $hate_speech_response = Http::timeout(10)
                            ->post($hate_speech_url . '/predict', [
                                'text' => $text
                            ]);

                        if ($hate_speech_response->successful()) {
                            $hate_data = $hate_speech_response->json();

                            if (isset($hate_data['isHateSpeech']) && $hate_data['isHateSpeech'] === true) {
                                $fail("Your image contains text with offensive language: '" . substr($text, 0, 50) . "...'");
                                return;
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Image hate speech validation error: ' . $e->getMessage());
            // Fail-open: if service is down, allow the image
        }
    }
}
