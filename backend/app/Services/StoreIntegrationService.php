<?php

namespace App\Services;

use App\Models\MockOrder;
use Illuminate\Support\Str;

class StoreIntegrationService
{
    /**
     * Query order tracking information by order number or customer phone.
     */
    public function checkOrderStatus(string $query, ?int $workspaceId = null): array
    {
        // Clean order number (remove # or prefix)
        $cleanNumber = trim(str_replace(['#', 'No.', 'no.', 'order', 'طلب'], '', $query));
        
        $orderQuery = MockOrder::query();
        if ($workspaceId) {
            $orderQuery->where(function($q) use ($workspaceId) {
                $q->where('workspace_id', $workspaceId)->orWhereNull('workspace_id');
            });
        }

        $order = $orderQuery->where('order_number', $cleanNumber)
            ->orWhere('order_number', 'like', "%{$cleanNumber}%")
            ->orWhere('customer_phone', 'like', "%{$cleanNumber}%")
            ->first();

        if ($order) {
            return [
                'found'              => true,
                'order_number'       => $order->order_number,
                'status'             => $order->status_label,
                'raw_status'         => $order->status,
                'courier'            => $order->courier ?: 'أرامكس (Aramex)',
                'tracking_number'    => $order->tracking_number ?: 'TRK-' . rand(100000, 999999),
                'items'              => $order->items_summary ?: 'منتجات المتجر',
                'total_amount'       => $order->total_amount . ' ر.س',
                'estimated_delivery' => $order->estimated_delivery ?: 'خلال 24-48 ساعة عمل',
                'tracking_url'       => "https://track.rudood.ai/" . ($order->tracking_number ?: 'TRK-982341'),
            ];
        }

        // Return a realistic simulation response if order wasn't specifically found in DB
        return [
            'found'              => false,
            'searched_number'    => $cleanNumber,
            'message'            => "لم يتم العثور على طلب بالرقم {$cleanNumber}. يرجى التأكد من كتابة رقم الطلب بالشكل الصحيح أو تزويدنا برقم الجوال المسجل به الطلب لمساعدتك فوراً.",
        ];
    }

    /**
     * Check product availability, current pricing, and checkout link.
     */
    public function checkProductStock(string $productName, ?int $workspaceId = null): array
    {
        $products = [
            'سماعة' => [
                'name'         => 'سماعات النخبة اللاسلكية (Pro Edition)',
                'price'        => 199.00,
                'is_available' => true,
                'stock'        => 18,
                'warranty'     => 'سنتين ضمان استبدال فوري',
                'delivery'     => 'شحن فوري خلال 24 ساعة',
                'checkout_url' => 'https://store.rudood.ai/checkout/elite-earbuds',
            ],
            'ساعة' => [
                'name'         => 'ساعة النخبة الرياضية الذكية AMOLED',
                'price'        => 299.00,
                'is_available' => true,
                'stock'        => 9,
                'warranty'     => 'سنة ضمان شامل',
                'delivery'     => 'شحن فوري داخل وخارج المملكة',
                'checkout_url' => 'https://store.rudood.ai/checkout/smart-watch',
            ],
            'شاحن' => [
                'name'         => 'منصة شحن لاسلكي سريع 3 في 1',
                'price'        => 149.00,
                'is_available' => true,
                'stock'        => 25,
                'warranty'     => 'ضمان سنتين',
                'delivery'     => 'متوفر للشحن الفوري',
                'checkout_url' => 'https://store.rudood.ai/checkout/wireless-charger',
            ],
        ];

        $productLower = Str::lower($productName);
        foreach ($products as $key => $details) {
            if (Str::contains($productLower, $key) || Str::contains(Str::lower($details['name']), $productLower)) {
                return array_merge(['found' => true], $details);
            }
        }

        return [
            'found'        => false,
            'product_name' => $productName,
            'message'      => "نعتذر، لم نعثر على منتج باسم '{$productName}'. يمكنك تصفح قسم المنتجات في المتجر أو تزويدنا بالموديل المطلوب بدقة.",
        ];
    }
}
