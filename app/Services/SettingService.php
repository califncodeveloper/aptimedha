<?php

namespace App\Services;

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Service to manage website configuration, contact information,
 * WhatsApp integration, and social media channels.
 */
class SettingService
{
    /**
     * In-memory cache for the current request cycle.
     *
     * @var array<string, mixed>|null
     */
    protected static ?array $memoizedSettings = null;

    /**
     * Default baseline settings preserving client design and existing contact details.
     *
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            // Contact settings
            'support_email' => 'support@aptimedha.com',
            'contact_email' => 'aptimedha@gmail.com',
            'contact_phone' => '+91 98765 43210',
            'whatsapp_number' => '+91 98765 43210',
            'whatsapp_url' => 'https://wa.me/919876543210',
            'contact_address' => 'India',

            // Social media channels
            'linkedin_url' => 'https://linkedin.com/company/aptimedha',
            'twitter_url' => 'https://twitter.com/aptimedha',
            'github_url' => '',
            'facebook_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',

            // General & Branding
            'site_title' => 'AptiMedha',
            'tagline' => 'Practise Smart. Get Placement Ready.',
            'meta_description' => 'Sharpen your aptitude, reasoning and subject skills to crack placement exams and interviews.',
        ];
    }

    /**
     * Get a specific setting value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::all();

        return $all[$key] ?? ($default ?? (static::defaults()[$key] ?? null));
    }

    /**
     * Get all settings merged with defaults.
     *
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        if (static::$memoizedSettings !== null) {
            return static::$memoizedSettings;
        }

        $merged = static::defaults();

        try {
            if (Schema::hasTable('website_settings')) {
                $dbSettings = WebsiteSetting::all()->pluck('value', 'key')->toArray();
                foreach ($dbSettings as $k => $v) {
                    if ($v !== null && $v !== '') {
                        $merged[$k] = $v;
                    }
                }
            }
        } catch (Throwable $e) {
            Log::warning('SettingService: Unable to load settings from database: ' . $e->getMessage());
        }

        // Ensure WhatsApp URL is auto-generated if phone number is provided without a custom link
        if (empty($merged['whatsapp_url']) && !empty($merged['whatsapp_number'])) {
            $merged['whatsapp_url'] = static::formatWhatsAppUrl($merged['whatsapp_number']);
        }

        static::$memoizedSettings = $merged;

        return static::$memoizedSettings;
    }

    /**
     * Update or create multiple settings.
     *
     * @param array<string, mixed> $data
     */
    public static function updateMany(array $data): void
    {
        $metaMap = [
            'support_email' => ['group' => 'contact', 'label' => 'Official Support Email', 'type' => 'email'],
            'contact_email' => ['group' => 'contact', 'label' => 'Contact Enquiry Recipient', 'type' => 'email'],
            'contact_phone' => ['group' => 'contact', 'label' => 'Contact Phone Number', 'type' => 'text'],
            'whatsapp_number' => ['group' => 'contact', 'label' => 'WhatsApp Contact Number', 'type' => 'text'],
            'whatsapp_url' => ['group' => 'contact', 'label' => 'WhatsApp Chat URL', 'type' => 'url'],
            'contact_address' => ['group' => 'contact', 'label' => 'Office / Contact Address', 'type' => 'text'],
            'linkedin_url' => ['group' => 'social', 'label' => 'LinkedIn URL', 'type' => 'url'],
            'twitter_url' => ['group' => 'social', 'label' => 'Twitter / X URL', 'type' => 'url'],
            'github_url' => ['group' => 'social', 'label' => 'GitHub Repository URL', 'type' => 'url'],
            'facebook_url' => ['group' => 'social', 'label' => 'Facebook Page URL', 'type' => 'url'],
            'instagram_url' => ['group' => 'social', 'label' => 'Instagram Profile URL', 'type' => 'url'],
            'youtube_url' => ['group' => 'social', 'label' => 'YouTube Channel URL', 'type' => 'url'],
            'site_title' => ['group' => 'general', 'label' => 'Site Title', 'type' => 'text'],
            'tagline' => ['group' => 'general', 'label' => 'Tagline / Motto', 'type' => 'text'],
            'meta_description' => ['group' => 'general', 'label' => 'Meta Description', 'type' => 'textarea'],
        ];

        // If whatsapp_number is provided and whatsapp_url is not specified, auto-generate it
        if (!empty($data['whatsapp_number']) && empty($data['whatsapp_url'])) {
            $data['whatsapp_url'] = static::formatWhatsAppUrl($data['whatsapp_number']);
        }

        try {
            if (Schema::hasTable('website_settings')) {
                foreach ($data as $key => $value) {
                    if (isset($metaMap[$key])) {
                        WebsiteSetting::updateOrCreate(
                            ['key' => $key],
                            [
                                'value' => $value !== null ? (string) $value : '',
                                'group' => $metaMap[$key]['group'],
                                'label' => $metaMap[$key]['label'],
                                'type' => $metaMap[$key]['type'],
                            ]
                        );
                    }
                }
            }
        } catch (Throwable $e) {
            Log::error('SettingService: Failed to update settings in database: ' . $e->getMessage());
        }

        static::clearCache();
    }

    /**
     * Clear memoized settings cache.
     */
    public static function clearCache(): void
    {
        static::$memoizedSettings = null;
    }

    /**
     * Format a phone number into an official WhatsApp web/api link.
     */
    public static function formatWhatsAppUrl(?string $number, ?string $explicitUrl = null): string
    {
        if (!empty($explicitUrl)) {
            return $explicitUrl;
        }

        if (empty($number)) {
            return '';
        }

        // Remove all non-digit characters
        $digits = preg_replace('/[^\d]/', '', $number);

        if (empty($digits)) {
            return '';
        }

        return 'https://wa.me/' . $digits;
    }
}
