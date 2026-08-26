<?php

namespace App\Services;

use App\Models\Channel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WhatsAppInteractiveService
{
    /**
     * Build Meta WhatsApp Cloud API v19.0 Interactive Quick Reply Buttons payload.
     * Meta limits buttons to maximum 3 items, with titles <= 20 characters.
     */
    public function buildButtonPayload(
        string $to,
        string $body,
        array $buttons,
        ?string $header = null,
        ?string $footer = 'ردود الذكاء الاصطناعي ⚡'
    ): array {
        $formattedButtons = [];
        $trimmedButtons = array_slice($buttons, 0, 3);

        foreach ($trimmedButtons as $idx => $btn) {
            $id    = is_array($btn) ? ($btn['id'] ?? 'btn_' . ($idx + 1)) : 'btn_' . ($idx + 1);
            $title = is_array($btn) ? ($btn['title'] ?? (string)$btn) : (string)$btn;
            $title = mb_substr(trim($title), 0, 20);

            $formattedButtons[] = [
                'type'  => 'reply',
                'reply' => [
                    'id'    => (string) $id,
                    'title' => $title,
                ]
            ];
        }

        $interactive = [
            'type'   => 'button',
            'body'   => ['text' => $body],
            'action' => [
                'buttons' => $formattedButtons
            ]
        ];

        if (!empty($header)) {
            $interactive['header'] = [
                'type' => 'text',
                'text' => mb_substr($header, 0, 60)
            ];
        }

        if (!empty($footer)) {
            $interactive['footer'] = [
                'text' => mb_substr($footer, 0, 60)
            ];
        }

        return [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => ltrim($to, '+'),
            'type'              => 'interactive',
            'interactive'       => $interactive,
        ];
    }

    /**
     * Build Meta WhatsApp Cloud API v19.0 Interactive List Menu payload.
     * Supports categorized sections and selectable rows with title + description.
     */
    public function buildListMenuPayload(
        string $to,
        string $body,
        string $buttonLabel = 'عرض الخيارات 📋',
        array $sections = [],
        ?string $header = null,
        ?string $footer = 'منصة ردود للأعمال'
    ): array {
        $formattedSections = [];
        $totalRows = 0;

        foreach ($sections as $section) {
            $sectionTitle = mb_substr($section['title'] ?? 'القائمة', 0, 24);
            $rows = [];

            foreach ($section['rows'] ?? [] as $row) {
                if ($totalRows >= 10) break; // Meta limit: 10 rows total per list

                $rows[] = [
                    'id'          => (string) ($row['id'] ?? 'row_' . ($totalRows + 1)),
                    'title'       => mb_substr($row['title'] ?? '', 0, 24),
                    'description' => isset($row['description']) ? mb_substr($row['description'], 0, 72) : '',
                ];
                $totalRows++;
            }

            if (!empty($rows)) {
                $formattedSections[] = [
                    'title' => $sectionTitle,
                    'rows'  => $rows,
                ];
            }
        }

        $interactive = [
            'type'   => 'list',
            'body'   => ['text' => $body],
            'action' => [
                'button'   => mb_substr($buttonLabel, 0, 20),
                'sections' => $formattedSections,
            ]
        ];

        if (!empty($header)) {
            $interactive['header'] = [
                'type' => 'text',
                'text' => mb_substr($header, 0, 60),
            ];
        }

        if (!empty($footer)) {
            $interactive['footer'] = [
                'text' => mb_substr($footer, 0, 60),
            ];
        }

        return [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => ltrim($to, '+'),
            'type'              => 'interactive',
            'interactive'       => $interactive,
        ];
    }

    /**
     * Build Interactive Product Carousel Cards payload.
     * Formats multiple product cards with images, prices, and direct checkout CTA.
     */
    public function buildProductCardsPayload(
        string $to,
        string $body,
        array $products
    ): array {
        $cards = [];
        foreach ($products as $idx => $prod) {
            $cards[] = [
                'id'           => $prod['id'] ?? 'prod_' . ($idx + 1),
                'title'        => $prod['name'] ?? $prod['title'] ?? 'منتج مميز',
                'description'  => $prod['description'] ?? 'متوفر للشحن الفوري مع ضمان شامل',
                'price'        => $prod['price'] ?? '0.00',
                'currency'     => $prod['currency'] ?? 'ر.س',
                'image_url'    => $prod['image_url'] ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500',
                'checkout_url' => $prod['checkout_url'] ?? 'https://store.rudood.ai/checkout/' . ($idx + 1),
                'button_text'  => $prod['button_text'] ?? 'شراء الآن 🛒',
            ];
        }

        return [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => ltrim($to, '+'),
            'type'              => 'interactive_carousel',
            'body'              => $body,
            'cards'             => $cards,
        ];
    }

    /**
     * Send structured interactive payload via Meta WhatsApp Cloud API.
     */
    public function sendInteractive(Channel $channel, string $toPhone, array $payload): array
    {
        if (!$channel->isActive() || empty($channel->access_token) || empty($channel->phone_number_id)) {
            return [
                'success' => false,
                'message' => 'WhatsApp channel is not connected or missing credentials.',
            ];
        }

        try {
            $endpoint = "https://graph.facebook.com/v19.0/{$channel->phone_number_id}/messages";
            $response = Http::withToken($channel->access_token)
                ->timeout(15)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json(),
                ];
            }

            \Log::warning('WhatsApp Cloud API interactive error: ' . $response->body());
            return [
                'success' => false,
                'error'   => $response->json() ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            \Log::error('WhatsApp interactive dispatch exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Pre-configured Quick Reply Template for Store Greetings.
     */
    public function getWelcomeButtons(): array
    {
        return [
            ['id' => 'btn_track_order',  'title' => '📦 تتبع طلبي'],
            ['id' => 'btn_view_catalog', 'title' => '🛍️ تصفح المنتجات'],
            ['id' => 'btn_agent_talk',   'title' => '👨‍💼 موظف بشري'],
        ];
    }

    /**
     * Pre-configured List Menu Template for Store Services.
     */
    public function getStoreServicesListMenu(): array
    {
        return [
            [
                'title' => 'الطلبات والشحنات',
                'rows'  => [
                    [
                        'id'          => 'menu_track_shipment',
                        'title'       => '📦 تتبع حالة الشحنة',
                        'description' => 'استعلام فوري عن مسار الشحنة برقم الطلب',
                    ],
                    [
                        'id'          => 'menu_shipping_policy',
                        'title'       => '🚚 أوقات وأسعار الشحن',
                        'description' => 'شحن سريع لجميع المدن والمناطق',
                    ],
                    [
                        'id'          => 'menu_return_request',
                        'title'       => '🔄 استبدال واسترجاع',
                        'description' => 'سياسة الاسترجاع السلس خلال 14 يوماً',
                    ],
                ]
            ],
            [
                'title' => 'المنتجات والعروض',
                'rows'  => [
                    [
                        'id'          => 'menu_catalog_all',
                        'title'       => '🛍️ تصفح الكتالوج',
                        'description' => 'عرض أحدث المنتجات والإلكترونيات الذكية',
                    ],
                    [
                        'id'          => 'menu_discount_offers',
                        'title'       => '🔥 العروض والخصومات',
                        'description' => 'أقوى التخفيضات الأسبوعية الحصرية',
                    ],
                ]
            ],
            [
                'title' => 'المساعدة والدعم',
                'rows'  => [
                    [
                        'id'          => 'menu_human_agent',
                        'title'       => '👨‍💼 خدمة العملاء',
                        'description' => 'التحويل المباشر إلى مستشار مبيعات بشري',
                    ],
                ]
            ],
        ];
    }

    /**
     * Pre-configured Product Catalog Cards for trending e-commerce items.
     */
    public function getFeaturedProductCards(): array
    {
        return [
            [
                'id'           => 'prod_earbuds_pro',
                'name'         => 'سماعات النخبة اللاسلكية (Pro Edition)',
                'description'  => 'عزل ضوضاء نشط ANC وبطارية تدوم حتى 36 ساعة مع علبة الشحن اللاسلكي.',
                'price'        => '199.00',
                'currency'     => 'ر.س',
                'image_url'    => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=500&auto=format&fit=crop&q=60',
                'checkout_url' => 'https://store.rudood.ai/checkout/elite-earbuds',
                'button_text'  => 'طلب فوري 🛍️',
            ],
            [
                'id'           => 'prod_smartwatch_amoled',
                'name'         => 'ساعة النخبة الرياضية الذكية AMOLED',
                'description'  => 'شاشة عالية الدقة، مراقبة نبضات القلب والأنشطة الرياضية، مقاومة للماء.',
                'price'        => '299.00',
                'currency'     => 'ر.س',
                'image_url'    => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=500&auto=format&fit=crop&q=60',
                'checkout_url' => 'https://store.rudood.ai/checkout/smart-watch',
                'button_text'  => 'طلب فوري 🛍️',
            ],
            [
                'id'           => 'prod_charger_3in1',
                'name'         => 'منصة شحن لاسلكي سريع 3 في 1',
                'description'  => 'شحن فوري للهاتف والساعة والسماعات في نفس الوقت بقوة 15 واط مع حماية ذكية.',
                'price'        => '149.00',
                'currency'     => 'ر.س',
                'image_url'    => 'https://images.unsplash.com/photo-1622445268462-34976722d3e3?w=500&auto=format&fit=crop&q=60',
                'checkout_url' => 'https://store.rudood.ai/checkout/wireless-charger',
                'button_text'  => 'طلب فوري 🛍️',
            ],
        ];
    }
}
