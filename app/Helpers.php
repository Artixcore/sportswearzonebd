<?php

if (! function_exists('storage_asset')) {
    /**
     * URL for a file in the public storage disk.
     * Uses a Laravel route so storage works when the filesystem symlink does not (e.g. Windows).
     */
    function storage_asset(string $path): string
    {
        $path = ltrim($path, '/');
        return url('storage-files/' . $path);
    }
}

if (! function_exists('cart_count')) {
    function cart_count(): int
    {
        $cart = session('cart', []);
        return (int) array_sum($cart);
    }
}

if (! function_exists('cart_items')) {
    function cart_items(): array
    {
        return session('cart', []);
    }
}
