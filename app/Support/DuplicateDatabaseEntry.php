<?php

namespace App\Support;

use Illuminate\Database\UniqueConstraintViolationException;

final class DuplicateDatabaseEntry
{
    /**
     * @return array{0: array<string, string>, 1: string}
     */
    public static function toValidationAndMessage(UniqueConstraintViolationException $e): array
    {
        $message = $e->getMessage();
        $key = self::extractIndexName($message);
        if ($key === null) {
            return [[], 'This record could not be saved because it duplicates existing data.'];
        }

        $fields = [];
        $lines = [];

        if (str_contains($key, 'slug')) {
            $msg = 'This URL slug is already used by another product. Choose a different slug.';
            $fields['slug'] = $msg;
            $lines[] = $msg;
        }
        if (str_contains($key, 'sku')) {
            $msg = 'This SKU is already in use. Enter a unique SKU.';
            $fields['sku'] = $msg;
            $lines[] = $msg;
        }

        if ($fields === []) {
            return [
                [],
                'This record could not be saved because it duplicates existing data. If the problem persists, contact support.',
            ];
        }

        return [$fields, implode(' ', $lines)];
    }

    private static function extractIndexName(string $message): ?string
    {
        if (preg_match("/for key '([^']+)'/i", $message, $m)) {
            return $m[1];
        }
        if (preg_match('/for key `([^`]+)`/i', $message, $m)) {
            return $m[1];
        }

        return null;
    }
}
