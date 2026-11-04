<?php

namespace App\services;

class WhatsAppPhoneNormalizer
{
    public static function normalize(?string $phone): string
    {
        // Remove spaces, +, dashes, brackets and all non-digit characters.
        $phone = preg_replace('/\D+/', '', $phone ?? '');

        // Convert Pakistani local mobile format:
        // 03244079039 -> 923244079039
        if (
            strlen($phone) === 11 &&
            str_starts_with($phone, '03')
        ) {
            $phone = '92' . substr($phone, 1);
        }

        // International format such as 923244079039 remains unchanged.
        return $phone;
    }
}