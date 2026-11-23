<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $connection = 'central';

    protected $table = 'core_plans';

    protected $fillable = [
        'audience',
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'currency',
        'trial_days',
        'site_limit',
        'storage_mb',
        'custom_domain',
        'commission_rate',
        'extra_site_price',
        'allowed_themes',
        'features',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'price_yearly' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'extra_site_price' => 'decimal:2',
            'custom_domain' => 'boolean',
            'allowed_themes' => 'array',
            'features' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function allowsTheme(string $moduleName): bool
    {
        if (empty($this->allowed_themes)) {
            return true;
        }

        return in_array($moduleName, $this->allowed_themes, true);
    }

    /**
     * Ensure default SaaS Client & Reseller Plans exist in core_plans.
     */
    public static function ensureDefaultsSeeded(): void
    {
        if (static::count() > 0) {
            return;
        }

        $defaults = [
            // Client Plans
            [
                'audience' => 'client',
                'name' => 'Single Theme Starter',
                'slug' => 'single-theme-starter',
                'description' => 'Perfect for launching 1 business website with isolated database & free subdomain.',
                'price_monthly' => 990,
                'price_yearly' => 9900,
                'currency' => 'BDT',
                'trial_days' => 14,
                'site_limit' => 1,
                'storage_mb' => 1024,
                'custom_domain' => false,
                'features' => [
                    '1 Ready-to-Deploy Industry Website',
                    'Dedicated Isolated MySQL Database',
                    '14-Day Free Trial Included',
                    'Free Platform Subdomain (.yourhub.com)',
                    '1 GB Media & Image Storage',
                ],
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'audience' => 'client',
                'name' => 'Business Pro (Custom Domain)',
                'slug' => 'business-pro',
                'description' => 'Connect your own custom domain with free Caddy Auto-SSL and up to 3 websites.',
                'price_monthly' => 2490,
                'price_yearly' => 24900,
                'currency' => 'BDT',
                'trial_days' => 14,
                'site_limit' => 3,
                'storage_mb' => 5120,
                'custom_domain' => true,
                'features' => [
                    'Up to 3 Industry Websites (Separate DBs)',
                    'Custom Domain Connection + Free Auto-SSL',
                    'All 11+ Industry Themes Unlocked',
                    '5 GB Fast Media Storage',
                    'Priority Technical Support',
                ],
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'audience' => 'client',
                'name' => 'Enterprise Multi-Brand',
                'slug' => 'enterprise-multi-brand',
                'description' => 'For companies running up to 10 websites across multiple industries.',
                'price_monthly' => 5990,
                'price_yearly' => 59900,
                'currency' => 'BDT',
                'trial_days' => 14,
                'site_limit' => 10,
                'storage_mb' => 20480,
                'custom_domain' => true,
                'features' => [
                    'Up to 10 Websites (10 Isolated Databases)',
                    'Unlimited Custom Domains & Auto-SSL',
                    'All 11+ Industry Themes Unlocked',
                    '20 GB Media Storage & Nightly Backups',
                    'Dedicated Account Manager',
                ],
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],

            // Reseller Partner Plans (Section 6.3)
            [
                'audience' => 'reseller',
                'name' => 'Reseller Starter (10 Sites)',
                'slug' => 'reseller-starter',
                'description' => 'Start selling websites under our platform with 10 client site slots or 15% referral commission.',
                'price_monthly' => 4500,
                'price_yearly' => 45000,
                'currency' => 'BDT',
                'trial_days' => 0,
                'site_limit' => 10,
                'storage_mb' => 5120,
                'custom_domain' => true,
                'commission_rate' => 15.00,
                'extra_site_price' => 450.00,
                'features' => [
                    '10 Client Website Slots (Isolated DBs)',
                    '15% Commission on Referred Clients (Mode B)',
                    'Custom Domains & Auto-SSL for Clients',
                    'One-Click Client Impersonation & Setup',
                ],
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'audience' => 'reseller',
                'name' => 'Reseller Business (25 Sites)',
                'slug' => 'reseller-business',
                'description' => 'For growing agencies managing up to 25 client websites or earning 20% commission.',
                'price_monthly' => 9500,
                'price_yearly' => 95000,
                'currency' => 'BDT',
                'trial_days' => 0,
                'site_limit' => 25,
                'storage_mb' => 15360,
                'custom_domain' => true,
                'commission_rate' => 20.00,
                'extra_site_price' => 380.00,
                'features' => [
                    '25 Client Website Slots (Isolated DBs)',
                    '20% Commission on Referred Clients (Mode B)',
                    'Wholesale Wallet Extra Site Rate: ৳380/site',
                    '15 GB Shared Agency Storage Pool',
                ],
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'audience' => 'reseller',
                'name' => 'Reseller Agency (50 Sites)',
                'slug' => 'reseller-agency',
                'description' => 'Maximum scale with 50 client website slots and 25% referral commission.',
                'price_monthly' => 16500,
                'price_yearly' => 165000,
                'currency' => 'BDT',
                'trial_days' => 0,
                'site_limit' => 50,
                'storage_mb' => 40960,
                'custom_domain' => true,
                'commission_rate' => 25.00,
                'extra_site_price' => 320.00,
                'features' => [
                    '50 Client Website Slots (Isolated DBs)',
                    '25% Commission on Referred Clients (Mode B)',
                    'Wholesale Wallet Extra Site Rate: ৳320/site',
                    '40 GB Storage Pool & Priority Support',
                ],
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($defaults as $planData) {
            static::create($planData);
        }
    }
}
