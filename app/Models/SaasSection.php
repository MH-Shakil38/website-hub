<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasSection extends Model
{
    protected $connection = 'central';

    protected $table = 'core_saas_sections';

    protected $fillable = [
        'section_key',
        'title',
        'content',
        'is_visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_visible' => 'boolean',
        ];
    }

    /**
     * Default SaaS Landing Page Sections (preserves the exact current hub-landing design).
     */
    public static function defaultSections(): array
    {
        return [
            'seo' => [
                'title' => 'SEO & Meta Tags',
                'sort_order' => 1,
                'is_visible' => true,
                'content' => [
                    'meta_title' => 'Modern Multi-Industry SaaS Website Platform',
                    'meta_description' => 'Launch production-ready industry websites in seconds with isolated databases, visual CMS, and instant domain routing.',
                ],
            ],
            'hero' => [
                'title' => 'Hero Banner Section',
                'sort_order' => 2,
                'is_visible' => true,
                'content' => [
                    'pill_tag' => 'New v3.5 Cloud',
                    'pill_text' => 'Ready-to-Deploy Industry Websites in One SaaS Hub',
                    'heading_line_1' => 'Launch Production-Ready',
                    'heading_gradient' => 'Industry Websites in Seconds',
                    'subheading' => 'The all-in-one modular SaaS website platform for agencies, enterprises, and creators. Activate any industry niche—from Real Estate, Healthcare & Pharmacy to Gym, Hajj & Kids Learning—with isolated databases, visual CMS, and instant domain routing.',
                    'primary_btn_text' => 'Explore Live Demos & Install',
                    'secondary_btn_text' => 'View Pricing & Free Trial',
                ],
            ],
            'themes' => [
                'title' => 'Theme Gallery Section (#themes)',
                'sort_order' => 3,
                'is_visible' => true,
                'content' => [
                    'eyebrow' => 'Live Interactive Demos & 1-Click Install',
                    'heading' => 'Select Your Industry Theme & Install in Seconds',
                    'subheading' => 'Click "Install Theme (Free Trial)" on any demo below to choose your subdomain, verify your email, and launch your dedicated database automatically.',
                ],
            ],
            'features' => [
                'title' => 'Bento Platform Features (#features)',
                'sort_order' => 4,
                'is_visible' => true,
                'content' => [
                    'eyebrow' => 'Enterprise SaaS Architecture',
                    'heading' => 'Built for Multi-Tenant Scale & Speed',
                    'subheading' => 'Engineered with strict multi-database isolation so you can host hundreds of client websites or reseller agencies with zero friction.',
                ],
            ],
            'pricing' => [
                'title' => 'Interactive Pricing Section (#pricing)',
                'sort_order' => 5,
                'is_visible' => true,
                'content' => [
                    'eyebrow' => 'Transparent SaaS Pricing',
                    'heading' => 'Flexible Plans with 14-Day Free Trial',
                    'subheading' => 'Click any plan below to jump to the Theme Gallery, select your preferred industry theme, and install immediately.',
                ],
            ],
            'faq' => [
                'title' => 'Frequently Asked Questions (#faq)',
                'sort_order' => 6,
                'is_visible' => true,
                'content' => [
                    'eyebrow' => 'Got Questions?',
                    'heading' => 'Frequently Asked Questions',
                    'subheading' => 'Everything you need to know about our multi-database SaaS website platform.',
                    'items' => [
                        [
                            'q' => 'How does the Step-by-Step Theme Installation & Free Trial work?',
                            'a' => 'Select any pricing plan or click "Install Theme (Free Trial)" on your favorite industry demo. Enter your desired subdomain and account details, then click the verification link sent to your email. As soon as your email is verified, our engine provisions a dedicated MySQL database and installs your theme!',
                        ],
                        [
                            'q' => 'Does every client website get its own isolated database?',
                            'a' => 'Yes! Every client website (tenant) runs in its own isolated MySQL database (hub_t_XXXX) with dedicated theme tables and an isolated uploads folder.',
                        ],
                        [
                            'q' => 'Can I connect my own custom domain with free SSL?',
                            'a' => 'Absolutely. From your Client Dashboard -> Custom Domains & SSL, add your domain, point your DNS A/CNAME record, and Caddy On-Demand TLS automatically issues a free Let\'s Encrypt SSL certificate.',
                        ],
                    ],
                ],
            ],
            'cta_footer' => [
                'title' => 'Bottom CTA Banner & Footer',
                'sort_order' => 7,
                'is_visible' => true,
                'content' => [
                    'cta_badge' => 'Ready for Instant Deployment',
                    'cta_heading' => 'Start Building Your Next Industry Website Today',
                    'cta_subheading' => 'Pick a theme, verify your email, and launch your isolated website in under 30 seconds.',
                    'footer_about' => 'Next-generation modular multi-website SaaS hub. Build, manage, and scale standalone industry websites from a single unified platform.',
                ],
            ],
        ];
    }

    /**
     * Ensure default sections are seeded and return a keyed array of sections for the landing page.
     */
    public static function getLandingMap(): array
    {
        $defaults = static::defaultSections();

        try {
            if (!\Illuminate\Support\Facades\Schema::connection('central')->hasTable('core_saas_sections')) {
                return $defaults;
            }

            if (static::count() === 0) {
                foreach ($defaults as $key => $cfg) {
                    static::create([
                        'section_key' => $key,
                        'title' => $cfg['title'],
                        'content' => $cfg['content'],
                        'is_visible' => $cfg['is_visible'],
                        'sort_order' => $cfg['sort_order'],
                    ]);
                }
            }

            $rows = static::orderBy('sort_order')->get()->keyBy('section_key');
            $result = [];
            foreach ($defaults as $key => $cfg) {
                if (isset($rows[$key])) {
                    $result[$key] = [
                        'title' => $rows[$key]->title ?: $cfg['title'],
                        'is_visible' => (bool) $rows[$key]->is_visible,
                        'sort_order' => $rows[$key]->sort_order,
                        'content' => array_replace_recursive($cfg['content'], $rows[$key]->content ?? []),
                    ];
                } else {
                    $result[$key] = $cfg;
                }
            }

            return $result;
        } catch (\Throwable) {
            return $defaults;
        }
    }
}
