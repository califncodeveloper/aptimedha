<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebsiteSetting;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNavigationAndContactSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        SettingService::clearCache();
        parent::tearDown();
    }

    public function test_cloudinary_test_and_generic_settings_are_hidden_from_admin_navigation(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();

        $content = $response->getContent();

        // Verify "Cloudinary Test" is not present in the navigation
        $this->assertStringNotContainsString('Cloudinary Test', $content);
        $this->assertStringNotContainsString(route('admin.cloudinary-test.index'), $content);

        // Verify generic "Settings" navigation label has been replaced with "Contact Settings"
        $this->assertStringContainsString('Contact Settings', $content);
        $this->assertStringContainsString(route('admin.settings.index'), $content);
    }

    public function test_admin_can_access_contact_settings_interface(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));
        $response->assertOk();

        // Verify contact settings headers and fields are present
        $response->assertSee('Contact & Social Media Settings', false);
        $response->assertSee('Official Support Email');
        $response->assertSee('Contact Enquiry Recipient');
        $response->assertSee('WhatsApp Contact Number');
        $response->assertSee('WhatsApp Direct Chat URL');
        $response->assertSee('LinkedIn Profile / Page');
        $response->assertSee('Twitter / X Handle');
        $response->assertDontSee('GitHub Repository');

        // Verify no Cloudinary credentials or configuration keys are exposed
        $response->assertDontSee('CLOUDINARY_API_KEY');
        $response->assertDontSee('CLOUDINARY_API_SECRET');
        $response->assertDontSee('Cloudinary Storage Test');
    }

    public function test_admin_can_update_contact_and_social_media_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $payload = [
            'support_email' => 'help@aptimedha.com',
            'contact_email' => 'office@aptimedha.com',
            'contact_phone' => '+91 91234 56789',
            'whatsapp_number' => '+91 91234 56789',
            'whatsapp_url' => 'https://wa.me/919123456789',
            'contact_address' => 'Hyderabad, India',
            'linkedin_url' => 'https://linkedin.com/company/aptimedha-official',
            'twitter_url' => 'https://twitter.com/aptimedha_app',
            'github_url' => 'https://github.com/aptimedha-org',
            'facebook_url' => 'https://facebook.com/aptimedha',
            'instagram_url' => 'https://instagram.com/aptimedha',
            'youtube_url' => 'https://youtube.com/@aptimedha',
            'active_tab' => 'contact',
        ];

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), $payload);
        $response->assertRedirect(route('admin.settings.index', ['tab' => 'contact']));
        $response->assertSessionHas('success', 'Settings updated successfully.');

        // Verify database persistence in website_settings table
        $this->assertDatabaseHas('website_settings', [
            'key' => 'support_email',
            'value' => 'help@aptimedha.com',
        ]);
        $this->assertDatabaseHas('website_settings', [
            'key' => 'whatsapp_number',
            'value' => '+91 91234 56789',
        ]);
        $this->assertDatabaseHas('website_settings', [
            'key' => 'whatsapp_url',
            'value' => 'https://wa.me/919123456789',
        ]);
        $this->assertDatabaseHas('website_settings', [
            'key' => 'linkedin_url',
            'value' => 'https://linkedin.com/company/aptimedha-official',
        ]);

        // Verify SettingService retrieves updated values
        SettingService::clearCache();
        $this->assertEquals('help@aptimedha.com', SettingService::get('support_email'));
        $this->assertEquals('+91 91234 56789', SettingService::get('whatsapp_number'));
        $this->assertEquals('https://wa.me/919123456789', SettingService::get('whatsapp_url'));
    }

    public function test_whatsapp_url_is_auto_generated_when_only_phone_number_is_provided(): void
    {
        $admin = User::factory()->admin()->create();

        $payload = [
            'whatsapp_number' => '+91 (987) 654-3210',
            'whatsapp_url' => '',
            'active_tab' => 'contact',
        ];

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), $payload);
        $response->assertRedirect();

        SettingService::clearCache();
        $this->assertEquals('https://wa.me/919876543210', SettingService::get('whatsapp_url'));
    }

    public function test_public_website_footer_renders_configured_social_and_whatsapp_links(): void
    {
        WebsiteSetting::create([
            'key' => 'whatsapp_url',
            'value' => 'https://wa.me/919876543210',
            'group' => 'contact',
            'label' => 'WhatsApp Chat URL',
        ]);
        WebsiteSetting::create([
            'key' => 'linkedin_url',
            'value' => 'https://linkedin.com/company/aptimedha',
            'group' => 'social',
            'label' => 'LinkedIn URL',
        ]);

        SettingService::clearCache();

        $response = $this->get(url('/'));
        $response->assertOk();

        // Footer should include WhatsApp support link and configured social links
        $response->assertSee('https://wa.me/919876543210', false);
        $response->assertSee('https://linkedin.com/company/aptimedha', false);
        $response->assertSee('title="WhatsApp Support"', false);
    }

    public function test_contact_page_renders_whatsapp_support_and_social_channels(): void
    {
        WebsiteSetting::create([
            'key' => 'whatsapp_number',
            'value' => '+91 98765 43210',
            'group' => 'contact',
            'label' => 'WhatsApp Number',
        ]);
        WebsiteSetting::create([
            'key' => 'whatsapp_url',
            'value' => 'https://wa.me/919876543210',
            'group' => 'contact',
            'label' => 'WhatsApp Chat URL',
        ]);

        SettingService::clearCache();

        $response = $this->get(route('contact'));
        $response->assertOk();

        // Contact page should display WhatsApp Support and link
        $response->assertSee('WhatsApp Support:');
        $response->assertSee('+91 98765 43210');
        $response->assertSee('https://wa.me/919876543210', false);
        $response->assertSee('Chat on WhatsApp');
    }

    public function test_contact_messages_index_provides_quick_link_to_contact_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.contacts.index'));
        $response->assertOk();

        $response->assertSee('Contact & Social Settings', false);
        $response->assertSee(route('admin.settings.index', ['tab' => 'contact']), false);
    }

    public function test_guest_and_regular_user_cannot_access_or_modify_settings(): void
    {
        // Guest
        $this->get(route('admin.settings.index'))->assertRedirect(route('login'));
        $this->put(route('admin.settings.update'), ['support_email' => 'hacked@example.com'])->assertRedirect(route('login'));

        // Regular authenticated user
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.settings.index'))->assertForbidden();
        $this->actingAs($user)->put(route('admin.settings.update'), ['support_email' => 'hacked@example.com'])->assertForbidden();
    }

    public function test_direct_cloudinary_routes_and_services_remain_unbroken(): void
    {
        // Unauthenticated guest is redirected
        $this->get(route('admin.cloudinary-test.index'))->assertRedirect(route('login'));

        // Authorized admin can access direct test utility
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get(route('admin.cloudinary-test.index'));
        $response->assertOk();
        $response->assertSee('Cloudinary Core Service Verification');
    }
}
