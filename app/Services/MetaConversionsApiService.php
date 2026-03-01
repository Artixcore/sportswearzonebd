<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class MetaConversionsApiService
{
    protected ?string $pixelId = null;
    protected ?string $accessToken = null;
    protected ?string $testCode = null;

    public function __construct()
    {
        $this->pixelId = Setting::get('meta_pixel_id', config('meta.pixel_id'));
        $this->accessToken = Setting::get('meta_access_token', config('meta.access_token'));
        $this->testCode = config('meta.test_event_code');
    }

    public function isConfigured(): bool
    {
        return !empty($this->pixelId) && !empty($this->accessToken);
    }

    public function sendEvent(string $eventName, string $eventId, array $userData = [], array $customData = []): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $payload = [
                'data' => [
                    [
                        'event_name' => $eventName,
                        'event_time' => time(),
                        'event_id' => $eventId,
                        'event_source_url' => url()->current(),
                        'action_source' => 'website',
                        'user_data' => $this->hashUserData($userData),
                        'custom_data' => $customData,
                    ],
                ],
                'access_token' => $this->accessToken,
            ];

            if ($this->testCode) {
                $payload['test_event_code'] = $this->testCode;
            }

            $response = Http::post("https://graph.facebook.com/v18.0/{$this->pixelId}/events", $payload);
            return $response->successful();
        } catch (Throwable $e) {
            Log::warning('Meta Conversions API sendEvent failed', ['event' => $eventName, 'message' => $e->getMessage()]);
            return false;
        }
    }

    public function sendViewContent(Product $product, string $eventId): bool
    {
        return $this->sendEvent('ViewContent', $eventId, [], [
            'content_type' => 'product',
            'content_ids' => [(string) $product->id],
            'content_name' => $product->name,
            'value' => (float) $product->price,
            'currency' => 'BDT',
        ]);
    }

    public function sendAddToCart(Product $product, int $quantity, string $eventId): bool
    {
        return $this->sendEvent('AddToCart', $eventId, [], [
            'content_type' => 'product',
            'content_ids' => [(string) $product->id],
            'content_name' => $product->name,
            'value' => (float) ($product->price * $quantity),
            'currency' => 'BDT',
            'num_items' => $quantity,
        ]);
    }

    public function sendInitiateCheckout(float $value, array $contentIds, string $eventId, array $userData = []): bool
    {
        return $this->sendEvent('InitiateCheckout', $eventId, $userData, [
            'content_type' => 'product',
            'content_ids' => array_map('strval', $contentIds),
            'value' => $value,
            'currency' => 'BDT',
            'num_items' => count($contentIds),
        ]);
    }

    public function sendPurchase(Order $order, string $eventId): bool
    {
        $userData = [];
        if ($order->user_id && $order->user) {
            $userData['em'] = $order->user->email;
            $userData['ph'] = $order->shipping_phone;
        } else {
            $userData['em'] = $order->guest_email;
            $userData['ph'] = $order->shipping_phone;
        }

        $contentIds = $order->items->pluck('product_id')->filter()->values()->map(fn ($id) => (string) $id)->toArray();
        if (empty($contentIds)) {
            $contentIds = $order->items->pluck('sku')->filter()->values()->toArray();
        }

        return $this->sendEvent('Purchase', $eventId, $userData, [
            'content_type' => 'product',
            'content_ids' => $contentIds,
            'value' => (float) $order->total,
            'currency' => $order->currency ?? 'BDT',
            'num_items' => $order->items->sum('quantity'),
            'order_id' => (string) $order->id,
        ]);
    }

    protected function hashUserData(array $data): array
    {
        $hashed = [];
        if (!empty($data['em'])) {
            $hashed['em'] = hash('sha256', strtolower(trim($data['em'])));
        }
        if (!empty($data['ph'])) {
            $hashed['ph'] = hash('sha256', preg_replace('/[^0-9]/', '', $data['ph']));
        }
        return $hashed;
    }
}
