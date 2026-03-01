<?php

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
