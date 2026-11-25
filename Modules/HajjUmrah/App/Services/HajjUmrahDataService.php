<?php

namespace Modules\HajjUmrah\App\Services;

use Illuminate\Support\Facades\Schema;
use Modules\HajjUmrah\App\Models\HajjBlog;
use Modules\HajjUmrah\App\Models\HajjInquiry;
use Modules\HajjUmrah\App\Models\HajjPackage;
use Modules\HajjUmrah\App\Models\HajjPartner;
use Modules\HajjUmrah\App\Models\HajjPricingPlan;
use Modules\HajjUmrah\App\Models\HajjService;
use Modules\HajjUmrah\App\Models\HajjSubscriber;
use Modules\HajjUmrah\App\Models\HajjTestimonial;
use Modules\HajjUmrah\App\Models\ModuleMedia;
use Modules\HajjUmrah\App\Models\ModuleMenu;
use Modules\HajjUmrah\App\Models\ModulePage;
use Modules\HajjUmrah\App\Models\ModuleSetting;
use Modules\HajjUmrah\App\Models\ModuleSlider;
use Modules\HajjUmrah\App\Models\ModuleTheme;
use Modules\HajjUmrah\App\Models\ModuleUser;
use Modules\HajjUmrah\App\Models\ModuleVideo;

class HajjUmrahDataService
{
    /**
     * Get merged settings & section content from Config/defaults.php + hajj_settings overrides.
     */
    public function getSettingsData(): array
    {
        $defaults = require module_path('HajjUmrah', 'Config/defaults.php');

        try {
            if (!Schema::connection('tenant')->hasTable('hajj_settings')) {
                return $defaults;
            }

            $raw = ModuleSetting::getValue('theme_sections_json', '');
            if (!empty($raw)) {
                $custom = json_decode($raw, true);
                if (is_array($custom)) {
                    $defaults = array_replace_recursive($defaults, $custom);
                    if (isset($custom['section_order']) && is_array($custom['section_order'])) {
                        $defaults['section_order'] = array_values($custom['section_order']);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Safely return defaults on any DB exception
        }

        return $defaults;
    }

    /**
     * Helper to resolve a localized value from a nested config array.
     * Example: $dataService->trans($t['hero'], 'headline', $locale)
     */
    public function trans(array $section, string $key, ?string $locale = null): string
    {
        $loc = $locale ?: session('hajj_locale', 'en');
        if ($loc !== 'en' && !empty($section["{$key}_{$loc}"])) {
            return (string) $section["{$key}_{$loc}"];
        }

        return (string) ($section[$key] ?? '');
    }

    /**
     * Ensure all hajj_* tables are seeded with the reference data if empty.
     */
    public function ensureSeeded(): void
    {
        try {
            if (!Schema::connection('tenant')->hasTable('hajj_packages')) {
                return;
            }

            // 1. Seed Special Offer Packages (Exact 4 cards from Reference Image 1 + additional Hajj/Umrah dropdown packages)
            if (HajjPackage::count() === 0) {
                $packages = [
                    [
                        'package_type'         => 'hajj',
                        'category_label'       => 'Category',
                        'category_label_bn'    => 'ক্যাটাগরি',
                        'category_label_ar'    => 'الفئة',
                        'title'                => 'Hajj Package',
                        'title_bn'             => 'হজ্জ প্যাকেজ (ভিআইপি)',
                        'title_ar'             => 'باقة الحج الفاخرة',
                        'slug'                 => 'vip-hajj-package',
                        'price'                => '$15,000',
                        'price_bn'             => '$১৫,০০০',
                        'price_ar'             => '$15,000',
                        'price_unit'           => '/Person',
                        'price_unit_bn'        => '/জনপ্রতি',
                        'price_unit_ar'        => '/للشخص',
                        'duration'             => '(15–25 Days)',
                        'duration_bn'          => '(১৫–২৫ দিন)',
                        'duration_ar'          => '(١٥–٢٥ يوماً)',
                        'thumbnail'            => '/module-media/HajjUmrah/theme/offer-passport.jpg',
                        'short_description'    => 'Premium 5-star Hajj package with direct Saudia flight, Haram-view hotel in Makkah & Madinah, and guided Ziyarah.',
                        'short_description_bn' => 'সরাসরি সৌদি এয়ারলাইন্স ফ্লাইট, মক্কা ও মদিনায় হারাম ভিউ ৫-স্টার হোটেল এবং গাইডেড জিয়ারাহ সহ প্রিমিয়াম হজ্জ প্যাকেজ।',
                        'short_description_ar' => 'باقة حج فاخرة ٥ نجوم مع طيران مباشر وفنادق مطلة على الحرم المكي والنبوي وزيارات ميدانية بإشراف مرشدين.',
                        'airline'              => 'Saudia / Biman Bangladesh',
                        'makkah_hotel'         => 'Swissôtel Makkah (100m from Haram)',
                        'madinah_hotel'        => 'Anwar Al Madinah Mövenpick (150m)',
                        'features'             => ['Visa & Insurance Included', 'Direct Return Air Ticket', '5-Star Buffet Full-Board Meals', 'Mina & Arafat VIP Air-Conditioned Tent'],
                        'features_bn'          => ['ভিসা ও হজ্জ ইন্স্যুরেন্স অন্তর্ভুক্ত', 'সরাসরি রিটার্ন এয়ার টিকেট', '৩ বেলা উন্নত বুফে খাবার', 'মিনা ও আরাফাতে এসি ভিআইপি তাবু'],
                        'features_ar'          => ['شامل التأشيرة والتأمين الصحي', 'تذكرة طيران مباشر ذهاب وعودة', 'بوفيه مفتوح ٣ وجبات يومياً', 'خيام مكيفة ومجهزة في منى وعرفات'],
                        'is_featured'          => true,
                        'sort_order'           => 1,
                    ],
                    [
                        'package_type'         => 'hajj',
                        'category_label'       => 'Category',
                        'category_label_bn'    => 'ক্যাটাগরি',
                        'category_label_ar'    => 'الفئة',
                        'title'                => 'Hajj Package',
                        'title_bn'             => 'ইকোনমি হজ্জ প্যাকেজ',
                        'title_ar'             => 'باقة الحج الاقتصادية',
                        'slug'                 => 'economy-hajj-package',
                        'price'                => '$12,000',
                        'price_bn'             => '$১২,০০০',
                        'price_ar'             => '$12,000',
                        'price_unit'           => '/Person',
                        'price_unit_bn'        => '/জনপ্রতি',
                        'price_unit_ar'        => '/للشخص',
                        'duration'             => '(15–25 Days)',
                        'duration_bn'          => '(১৫–২৫ দিন)',
                        'duration_ar'          => '(١٥–٢٥ يوماً)',
                        'thumbnail'            => '/module-media/HajjUmrah/theme/offer-family.jpg',
                        'short_description'    => 'Comfortable family & group Hajj package with dedicated Muallim, convenient hotel proximity, and full transport.',
                        'short_description_bn' => 'অভিজ্ঞ মুয়াল্লিম, হারাম শরীফের নিকটবর্তী আরামদায়ক হোটেল এবং সম্পূর্ণ পরিবহন সুবিধাসহ ফ্যামিলি ও গ্রুপ হজ্জ প্যাকেজ।',
                        'short_description_ar' => 'باقة حج مريحة للعائلات والمجموعات مع مرشد ديني مختص وفنادق قريبة ومواصلات كاملة.',
                        'airline'              => 'Saudia / Flynas',
                        'makkah_hotel'         => '350m from Masjid Al-Haram',
                        'madinah_hotel'        => '300m from Masjid An-Nabawi',
                        'features'             => ['Complete Hajj Visa Processing', 'Direct Flight Ticket', '3 Daily Bangladeshi & Continental Meals', 'Full Ziyarah in Makkah & Madinah'],
                        'features_bn'          => ['সম্পূর্ণ হজ্জ ভিসা প্রসেসিং', 'সরাসরি ফ্লাইট টিকেট', 'প্রতিদিন ৩ বেলা দেশি ও কন্টিনেন্টাল খাবার', 'মক্কা ও মদিনায় পূর্ণাঙ্গ জিয়ারাহ'],
                        'features_ar'          => ['إنجاز كامل لإجراءات تأشيرة الحج', 'تذكرة طيران مباشر', '٣ وجبات يومية متنوعة وصحية', 'زيارات كاملة للمشاعر المقدسة'],
                        'is_featured'          => true,
                        'sort_order'           => 2,
                    ],
                    [
                        'package_type'         => 'umrah',
                        'category_label'       => 'Category',
                        'category_label_bn'    => 'ক্যাটাগরি',
                        'category_label_ar'    => 'الفئة',
                        'title'                => 'Umrah Package',
                        'title_bn'             => 'প্রিমিয়াম ওমরাহ প্যাকেজ',
                        'title_ar'             => 'باقة العمرة المميزة',
                        'slug'                 => 'premium-umrah-package',
                        'price'                => '$10,000',
                        'price_bn'             => '$১০,০০০',
                        'price_ar'             => '$10,000',
                        'price_unit'           => '/Person',
                        'price_unit_bn'        => '/জনপ্রতি',
                        'price_unit_ar'        => '/للشخص',
                        'duration'             => '(10–14 Days)',
                        'duration_bn'          => '(১০–১৪ দিন)',
                        'duration_ar'          => '(١٠–١٤ يوماً)',
                        'thumbnail'            => '/module-media/HajjUmrah/theme/offer-mosque.jpg',
                        'short_description'    => 'Spiritual 14-day Umrah package staying steps away from Masjid Al-Haram and Rawdah Sharif with Haramain High-Speed Train.',
                        'short_description_bn' => 'মসজিদে হারাম ও রওজা শরীফের সন্নিকটে আবাসন এবং হারামাইন হাই-স্পিড ট্রেন ভ্রমণসহ ১৪ দিনের প্রিমিয়াম ওমরাহ প্যাকেজ।',
                        'short_description_ar' => 'باقة عمرة روحانية لمدة ١٤ يوماً مع إقامة بجوار الحرم المكي والروضة الشريفة وقطار الحرمين السريع.',
                        'airline'              => 'Qatar Airways / Saudia',
                        'makkah_hotel'         => 'Hilton Suites Makkah (100m)',
                        'madinah_hotel'        => 'Pullman Zamzam Madinah (150m)',
                        'features'             => ['Instant Umrah E-Visa', 'Haramain High-Speed Bullet Train', 'Guided Umrah Performance', 'Historical Ziyarah Tours'],
                        'features_bn'          => ['দ্রুত ওমরাহ ই-ভিসা', 'হারামাইন হাই-স্পিড বুলেট ট্রেন', 'মুয়াল্লিমের তত্ত্বাবধানে ওমরাহ পালন', 'ঐতিহাসিক স্থানসমূহে জিয়ারাহ ট্যুর'],
                        'features_ar'          => ['تأشيرة عمرة إلكترونية فورية', 'قطار الحرمين السريع بين مكة والمدينة', 'إرشاد ديني كامل لأداء العمرة', 'جولات زيارة للمعالم التاريخية'],
                        'is_featured'          => true,
                        'sort_order'           => 3,
                    ],
                    [
                        'package_type'         => 'umrah',
                        'category_label'       => 'Category',
                        'category_label_bn'    => 'ক্যাটাগরি',
                        'category_label_ar'    => 'الفئة',
                        'title'                => 'Umrah Package',
                        'title_bn'             => 'ফ্যামিলি ওমরাহ প্যাকেজ',
                        'title_ar'             => 'باقة العمرة العائلية',
                        'slug'                 => 'family-umrah-package',
                        'price'                => '$7,000',
                        'price_bn'             => '$৭,০০০',
                        'price_ar'             => '$7,000',
                        'price_unit'           => '/Person',
                        'price_unit_bn'        => '/জনপ্রতি',
                        'price_unit_ar'        => '/للشخص',
                        'duration'             => '(10–14 Days)',
                        'duration_bn'          => '(১০–১৪ দিন)',
                        'duration_ar'          => '(١٠–١٤ يوماً)',
                        'thumbnail'            => '/module-media/HajjUmrah/theme/offer-kaaba-door.jpg',
                        'short_description'    => 'Budget-friendly all-inclusive family Umrah package with private airport transfers, hotel accommodation, and 24/7 support.',
                        'short_description_bn' => 'প্রাইভেট এয়ারপোর্ট ট্রান্সফার, আরামদায়ক হোটেল আবাসন এবং ২৪/৭ সাপোর্টসহ সাশ্রয়ী অল-ইনক্লুসিভ ফ্যামিলি ওমরাহ প্যাকেজ।',
                        'short_description_ar' => 'باقة عمرة عائلية شاملة وبأسعار مناسبة مع استقبال خاص في المطار وإقامة مريحة ودعم على مدار الساعة.',
                        'airline'              => 'Saudia / Biman / Flynas',
                        'makkah_hotel'         => '400m from Masjid Al-Haram',
                        'madinah_hotel'        => '350m from Masjid An-Nabawi',
                        'features'             => ['Umrah Visa & Insurance', 'Private Family Transport', 'Breakfast, Lunch & Dinner', '5 Liters Zamzam Water Gift'],
                        'features_bn'          => ['ওমরাহ ভিসা ও ইন্স্যুরেন্স', 'প্রাইভেট ফ্যামিলি ট্রান্সপোর্ট', 'সকাল, দুপুর ও রাতের খাবার', '৫ লিটার জমজম পানি উপহার'],
                        'features_ar'          => ['تأشيرة العمرة والتأمين', 'مواصلات خاصة للعائلة', 'وجبات الإفطار والغداء والعشاء', 'هدية ماء زمزم ٥ لتر لكل معتمر'],
                        'is_featured'          => true,
                        'sort_order'           => 4,
                    ],
                ];

                foreach ($packages as $pkg) {
                    HajjPackage::create(array_merge($pkg, ['is_active' => true]));
                }
            }

            // 2. Seed Dedicated 3-Column All Inclusive Pricing Plans (Exact match to Reference Image 2)
            if (HajjPricingPlan::count() === 0) {
                $plans = [
                    [
                        'name'                => 'Economy Package',
                        'name_bn'             => 'ইকোনমি প্যাকেজ',
                        'name_ar'             => 'الباقة الاقتصادية',
                        'slug'                => 'economy-package',
                        'subtitle'            => 'Experience a fulfilling hajj 2028 with our economy package in your budget.',
                        'subtitle_bn'         => 'আপনার বাজেটের মধ্যে আমাদের ইকোনমি প্যাকেজে ২০২৮ সালের পরিপূর্ণ হজ্জ পালন করুন।',
                        'subtitle_ar'         => 'استمتع بأداء فريضة حج ٢٠٢٨ متكاملة وميسرة ضمن ميزانيتك مع باقتنا الاقتصادية.',
                        'currency'            => 'BDT',
                        'currency_bn'         => 'টাকা',
                        'currency_ar'         => 'تاكا',
                        'price'               => '6,35,000',
                        'price_bn'            => '৬,৩৫,০০০',
                        'price_ar'            => '6,35,000',
                        'price_suffix'        => 'STARTS FROM',
                        'price_suffix_bn'     => 'শুরু মূল্য',
                        'price_suffix_ar'     => 'يبدأ من',
                        'icon_class'          => 'fa-solid fa-hand-holding-dollar',
                        'duration'            => '40/42 Days',
                        'duration_bn'         => '৪০/৪২ দিন',
                        'duration_ar'         => '٤٠/٤٢ يوماً',
                        'direct_flight'       => 'SA/Biman/Flynas',
                        'direct_flight_bn'    => 'সৌদি/বিমান/ফ্লাইনাস',
                        'direct_flight_ar'    => 'الخطوط السعودية / بيمان / طيران ناس',
                        'hotel_aziziyah'      => 'Near to Jamarah',
                        'hotel_aziziyah_bn'   => 'জামারাতের সন্নিকটে',
                        'hotel_aziziyah_ar'   => 'بالقرب من الجمرات',
                        'hotel_makkah'        => '600/700M',
                        'hotel_makkah_bn'     => '৬০০/৭০০ মিটার',
                        'hotel_makkah_ar'     => '٦٠٠/٧٠٠ متر من الحرم',
                        'hotel_madinah'       => '500/600M',
                        'hotel_madinah_bn'    => '৫০০/৬০০ মিটার',
                        'hotel_madinah_ar'    => '٥٠٠/٦٠٠ متر من الحرم',
                        'food'                => 'Breakfast, Lunch & Dinner',
                        'food_bn'             => 'সকালের নাস্তা, দুপুর ও রাতের খাবার',
                        'food_ar'             => 'الإفطار والغداء والعشاء',
                        'special_services'    => 'Ziyarah Tour, Transportation & Guide',
                        'special_services_bn' => 'জিয়ারাহ ট্যুর, পরিবহন ও অভিজ্ঞ গাইড',
                        'special_services_ar' => 'جولات الزيارة والمواصلات والمرشد الديني',
                        'button_text'         => 'SELECT PACKAGE',
                        'button_text_bn'      => 'প্যাকেজ নির্বাচন করুন',
                        'button_text_ar'      => 'اختر هذه الباقة',
                        'terms_text'          => '*Terms & Conditions Applicable',
                        'terms_text_bn'       => '*শর্তাবলী প্রযোজ্য',
                        'terms_text_ar'       => '*تطبق الشروط والأحكام',
                        'is_featured'         => false,
                        'sort_order'          => 1,
                    ],
                    [
                        'name'                => 'Standard Package',
                        'name_bn'             => 'স্ট্যান্ডার্ড প্যাকেজ',
                        'name_ar'             => 'الباقة القياسية المميزة',
                        'slug'                => 'standard-package',
                        'subtitle'            => 'Our standard hajj 2028 blends essential comfort with spiritual excellence.',
                        'subtitle_bn'         => 'আমাদের স্ট্যান্ডার্ড হজ্জ ২০২৮ প্যাকেজে রয়েছে অপরিহার্য আরাম ও আধ্যাত্মিক প্রশান্তির অপূর্ব সমন্বয়।',
                        'subtitle_ar'         => 'تجمع باقة الحج القياسية ٢٠٢٨ بين الراحة الفائقة والتميز الروحاني بالقرب من الحرمين.',
                        'currency'            => 'BDT',
                        'currency_bn'         => 'টাকা',
                        'currency_ar'         => 'تاكا',
                        'price'               => '7,35,000',
                        'price_bn'            => '৭,৩৫,০০০',
                        'price_ar'            => '7,35,000',
                        'price_suffix'        => 'STARTS FROM',
                        'price_suffix_bn'     => 'শুরু মূল্য',
                        'price_suffix_ar'     => 'يبدأ من',
                        'icon_class'          => 'fa-solid fa-crown',
                        'duration'            => '40/42 Days',
                        'duration_bn'         => '৪০/৪২ দিন',
                        'duration_ar'         => '٤٠/٤٢ يوماً',
                        'direct_flight'       => 'SA/Biman/Flynas',
                        'direct_flight_bn'    => 'সৌদি/বিমান/ফ্লাইনাস',
                        'direct_flight_ar'    => 'الخطوط السعودية / بيمان / طيران ناس',
                        'hotel_aziziyah'      => 'Near to Jamarah',
                        'hotel_aziziyah_bn'   => 'জামারাতের সন্নিকটে',
                        'hotel_aziziyah_ar'   => 'بالقرب من الجمرات',
                        'hotel_makkah'        => '300/500M',
                        'hotel_makkah_bn'     => '৩০০/৫০০ মিটার',
                        'hotel_makkah_ar'     => '٣٠٠/٥٠٠ متر من الحرم',
                        'hotel_madinah'       => '300/400M',
                        'hotel_madinah_bn'    => '৩০০/৪০০ মিটার',
                        'hotel_madinah_ar'    => '٣٠٠/٤٠٠ متر من الحرم',
                        'food'                => 'Breakfast, Lunch & Dinner',
                        'food_bn'             => 'সকালের নাস্তা, দুপুর ও রাতের খাবার',
                        'food_ar'             => 'الإفطار والغداء والعشاء',
                        'special_services'    => 'Ziyarah Tour, Transportation & Guide',
                        'special_services_bn' => 'জিয়ারাহ ট্যুর, পরিবহন ও অভিজ্ঞ গাইড',
                        'special_services_ar' => 'جولات الزيارة والمواصلات والمرشد الديني',
                        'button_text'         => 'SELECT PACKAGE',
                        'button_text_bn'      => 'প্যাকেজ নির্বাচন করুন',
                        'button_text_ar'      => 'اختر هذه الباقة',
                        'terms_text'          => '*Terms & Conditions Applicable',
                        'terms_text_bn'       => '*শর্তাবলী প্রযোজ্য',
                        'terms_text_ar'       => '*تطبق الشروط والأحكام',
                        'is_featured'         => true, // Dark Emerald Green Highlighted Card!
                        'sort_order'          => 2,
                    ],
                    [
                        'name'                => 'Classic Package',
                        'name_bn'             => 'ক্লাসিক প্যাকেজ',
                        'name_ar'             => 'الباقة الكلاسيكية الفاخرة',
                        'slug'                => 'classic-package',
                        'subtitle'            => 'Elevated hajj 2028 with luxury amenities for your spiritual journey.',
                        'subtitle_bn'         => 'আপনার পবিত্র হজ্জ সফরকে সর্বোচ্চ আরামদায়ক করতে বিলাসবহুল সুবিধাসম্বলিত ক্লাসিক প্যাকেজ।',
                        'subtitle_ar'         => 'ارتقِ برحلة حج ٢٠٢٨ مع أرقى وسائل الراحة والفنادق الملاصقة للحرمين الشريفين.',
                        'currency'            => 'BDT',
                        'currency_bn'         => 'টাকা',
                        'currency_ar'         => 'تاكا',
                        'price'               => '8,85,000',
                        'price_bn'            => '৮,৮৫,০০০',
                        'price_ar'            => '8,85,000',
                        'price_suffix'        => 'STARTS FROM',
                        'price_suffix_bn'     => 'শুরু মূল্য',
                        'price_suffix_ar'     => 'يبدأ من',
                        'icon_class'          => 'fa-regular fa-gem',
                        'duration'            => '40/42 Days',
                        'duration_bn'         => '৪০/৪২ দিন',
                        'duration_ar'         => '٤٠/٤٢ يوماً',
                        'direct_flight'       => 'SA/Biman/Flynas',
                        'direct_flight_bn'    => 'সৌদি/বিমান/ফ্লাইনাস',
                        'direct_flight_ar'    => 'الخطوط السعودية / بيمان / طيران ناس',
                        'hotel_aziziyah'      => 'Near to Jamarah',
                        'hotel_aziziyah_bn'   => 'জামারাতের সন্নিকটে',
                        'hotel_aziziyah_ar'   => 'بالقرب من الجمرات',
                        'hotel_makkah'        => '100/100M',
                        'hotel_makkah_bn'     => '১০০/১০০ মিটার',
                        'hotel_makkah_ar'     => '١٠٠/١٠٠ متر من الحرم',
                        'hotel_madinah'       => '200/300M',
                        'hotel_madinah_bn'    => '২০০/৩০০ মিটার',
                        'hotel_madinah_ar'    => '٢٠٠/٣٠٠ متر من الحرم',
                        'food'                => 'Breakfast, Lunch & Dinner',
                        'food_bn'             => 'সকালের নাস্তা, দুপুর ও রাতের খাবার',
                        'food_ar'             => 'الإفطار والغداء والعشاء',
                        'special_services'    => 'Ziyarah Tour, Transportation & Guide',
                        'special_services_bn' => 'জিয়ারাহ ট্যুর, পরিবহন ও অভিজ্ঞ গাইড',
                        'special_services_ar' => 'جولات الزيارة والمواصلات والمرشد الديني',
                        'button_text'         => 'SELECT PACKAGE',
                        'button_text_bn'      => 'প্যাকেজ নির্বাচন করুন',
                        'button_text_ar'      => 'اختر هذه الباقة',
                        'terms_text'          => '*Terms & Conditions Applicable',
                        'terms_text_bn'       => '*শর্তাবলী প্রযোজ্য',
                        'terms_text_ar'       => '*تطبق الشروط والأحكام',
                        'is_featured'         => false,
                        'sort_order'          => 3,
                    ],
                ];

                foreach ($plans as $plan) {
                    HajjPricingPlan::create(array_merge($plan, ['is_active' => true]));
                }
            }

            // 3. Seed 6 Services ("What Do We Offer" - Exact 6 items from Reference Image 2)
            if (HajjService::count() === 0) {
                $services = [
                    [
                        'title'          => 'Air Ticketing',
                        'title_bn'       => 'এয়ার টিকেটিং',
                        'title_ar'       => 'حجوزات الطيران',
                        'slug'           => 'air-ticketing',
                        'subtitle'       => 'International & Domestic',
                        'subtitle_bn'    => 'আন্তর্জাতিক ও অভ্যন্তরীণ ফ্লাইট',
                        'subtitle_ar'    => 'رحلات دولية ومحلية مباشرة',
                        'description'    => 'Direct flight tickets on Saudia, Biman Bangladesh, Qatar Airways, and Flynas with generous Zamzam & luggage allowance.',
                        'description_bn' => 'সৌদি এয়ারলাইন্স, বিমান বাংলাদেশ ও কাতার এয়ারওয়েজে সরাসরি ফ্লাইট ও জমজম বহনের সুবিধা।',
                        'description_ar' => 'تذاكر طيران مباشر على الخطوط السعودية وبيمان والقطرية مع وزن أمتعة إضافي وماء زمزم.',
                        'icon_class'     => 'fa-solid fa-plane',
                        'sort_order'     => 1,
                    ],
                    [
                        'title'          => 'Tour Guide',
                        'title_bn'       => 'অভিজ্ঞ মুয়াল্লিম ও গাইড',
                        'title_ar'       => 'مرشد سياحي وديني',
                        'slug'           => 'tour-guide',
                        'subtitle'       => 'Experienced Guides',
                        'subtitle_bn'    => 'আলেম ও অভিজ্ঞ গাইড',
                        'subtitle_ar'    => 'مرشدون ذوو خبرة عالية',
                        'description'    => 'Certified Islamic scholars and multilingual Muallims accompanying you throughout Tawaf, Saee, Mina, Arafat, and Madinah Ziyarah.',
                        'description_bn' => 'তাওয়াফ, সাঈ, মিনা, আরাফাত এবং মদিনা জিয়ারাহর প্রতিটি ধাপে বাংলা, আরবি ও ইংরেজিভাষী অভিজ্ঞ আলেম মুয়াল্লিম।',
                        'description_ar' => 'علماء ومرشدون معتمدون يرافقونكم في الطواف والسعي ومنى وعرفات وزيارات المدينة المنورة.',
                        'icon_class'     => 'fa-solid fa-user-tie',
                        'sort_order'     => 2,
                    ],
                    [
                        'title'          => 'Hotel Booking',
                        'title_bn'       => 'হোটেল বুকিং',
                        'title_ar'       => 'حجوزات الفنادق',
                        'slug'           => 'hotel-booking',
                        'subtitle'       => 'Premium & Budget Hotels',
                        'subtitle_bn'    => 'প্রিমিয়াম ও বাজেট হোটেল',
                        'subtitle_ar'    => 'فنادق فاخرة واقتصادية',
                        'description'    => 'Handpicked 3-star to 5-star hotels within walking distance (100m–600m) of Masjid Al-Haram in Makkah and Masjid An-Nabawi in Madinah.',
                        'description_bn' => 'মক্কার মসজিদে হারাম ও মদিনার মসজিদে নববীর পায়ে হাঁটা দূরত্বে (১০০-৬০০ মিটার) উন্নত মানের ৩-স্টার ও ৫-স্টার হোটেল।',
                        'description_ar' => 'فنادق مختارة بعناية من ٣ إلى ٥ نجوم على مسافة قريبة سيراً على الأقدام من الحرمين الشريفين.',
                        'icon_class'     => 'fa-solid fa-bed',
                        'sort_order'     => 3,
                    ],
                    [
                        'title'          => 'Food Services',
                        'title_bn'       => 'হালাল খাবার সেবা',
                        'title_ar'       => 'خدمات الإعاشة والوجبات',
                        'slug'           => 'food-services',
                        'subtitle'       => 'Healthy & Halal Food',
                        'subtitle_bn'    => 'স্বাস্থ্যসম্মত ও হালাল খাবার',
                        'subtitle_ar'    => 'طعام صحي وحلال طازج',
                        'description'    => 'Freshly prepared hygienic breakfast, lunch, and dinner served daily by expert Bangladeshi & Arabian chefs.',
                        'description_bn' => 'দক্ষ দেশি ও আরব বাবুর্চিদের দ্বারা প্রস্তুতকৃত প্রতিদিন ৩ বেলা সুস্বাদু ও স্বাস্থ্যসম্মত হালাল খাবার।',
                        'description_ar' => 'وجبات إفطار وغداء وعشاء صحية وطازجة يومياً بإشراف طهاة متخصصين.',
                        'icon_class'     => 'fa-solid fa-utensils',
                        'sort_order'     => 4,
                    ],
                    [
                        'title'          => 'Transport Services',
                        'title_bn'       => 'পরিবহন সেবা',
                        'title_ar'       => 'خدمات النقل والمواصلات',
                        'slug'           => 'transport-services',
                        'subtitle'       => 'Airport to Makkah & Madinah',
                        'subtitle_bn'    => 'এয়ারপোর্ট থেকে মক্কা ও মদিনা',
                        'subtitle_ar'    => 'من المطار إلى مكة والمدينة',
                        'description'    => 'Luxury air-conditioned VIP buses, private GMC SUVs, and Haramain High-Speed Rail tickets for seamless intercity transfers.',
                        'description_bn' => 'বিলাসবহুল এসি ভিআইপি বাস, প্রাইভেট কার এবং হারামাইন হাই-স্পিড ট্রেনের মাধ্যমে আরামদায়ক যাতায়াত।',
                        'description_ar' => 'حافلات فاخرة مكيفة وسيارات خاصة وتذاكر قطار الحرمين السريع لتنقل مريح وآمن.',
                        'icon_class'     => 'fa-solid fa-bus',
                        'sort_order'     => 5,
                    ],
                    [
                        'title'          => '24/7 Support',
                        'title_bn'       => '২৪/৭ সার্বক্ষণিক সহায়তা',
                        'title_ar'       => 'دعم متواصل ٢٤/٧',
                        'slug'           => '24-7-support',
                        'subtitle'       => 'Always With You',
                        'subtitle_bn'    => 'সবসময় আপনার পাশে',
                        'subtitle_ar'    => 'معكم في كل خطوة',
                        'description'    => 'Dedicated Makkah & Madinah ground medical and logistics teams available 24 hours a day for your peace of mind.',
                        'description_bn' => 'মক্কা ও মদিনায় আমাদের নিজস্ব মেডিকেল ও সাপোর্ট টিম দিন-রাত ২৪ ঘণ্টা হাজীদের সেবায় নিয়োজিত।',
                        'description_ar' => 'فرق طبية وإدارية ميدانية في مكة والمدينة متوفرة على مدار الساعة لخدمتكم وراحتكم.',
                        'icon_class'     => 'fa-solid fa-headset',
                        'sort_order'     => 6,
                    ],
                ];

                foreach ($services as $srv) {
                    HajjService::create(array_merge($srv, ['is_active' => true]));
                }
            }

            // 4. Seed Partners (5 Trust Review Badges from Screenshot 1 + 6 Airlines & Hotels from Screenshot 2)
            if (HajjPartner::count() === 0) {
                $partners = [
                    // Top Trust Review Strip (Screenshot 1)
                    ['name' => 'Trustpilot', 'type' => 'review_badge', 'rating' => '4.5/5', 'reviews_text' => '1597 reviews', 'reviews_text_bn' => '১৫৯৭ রিভিউ', 'reviews_text_ar' => '١٥٩٧ تقييم', 'sort_order' => 1],
                    ['name' => 'facebook', 'type' => 'review_badge', 'rating' => '4/5', 'reviews_text' => '3701 reviews', 'reviews_text_bn' => '৩৭০১ রিভিউ', 'reviews_text_ar' => '٣٧٠١ تقييم', 'sort_order' => 2],
                    ['name' => 'The Real Yellow Pages', 'type' => 'review_badge', 'rating' => '4.5/5', 'reviews_text' => '2467 reviews', 'reviews_text_bn' => '২৪৬৭ রিভিউ', 'reviews_text_ar' => '٢٤٦٧ تقييم', 'sort_order' => 3],
                    ['name' => 'BAAT UMRAH TRAVEL', 'type' => 'review_badge', 'rating' => '5/5', 'reviews_text' => '371 reviews', 'reviews_text_bn' => '৩৭১ রিভিউ', 'reviews_text_ar' => '٣٧١ تقييم', 'sort_order' => 4],
                    ['name' => 'Angi', 'type' => 'review_badge', 'rating' => '5/5', 'reviews_text' => '317 reviews', 'reviews_text_bn' => '৩১৭ রিভিউ', 'reviews_text_ar' => '٣١٧ تقييم', 'sort_order' => 5],

                    // Affiliated Airlines & Hotels (Screenshot 2: Saudia, Turkish Airlines, Qatar Airways, Singapore Airlines, Hilton, Hyatt)
                    ['name' => 'Saudia', 'type' => 'airline', 'sort_order' => 1],
                    ['name' => 'Turkish Airlines', 'type' => 'airline', 'sort_order' => 2],
                    ['name' => 'Qatar Airways', 'type' => 'airline', 'sort_order' => 3],
                    ['name' => 'Singapore Airlines', 'type' => 'airline', 'sort_order' => 4],
                    ['name' => 'Hilton', 'type' => 'hotel', 'sort_order' => 5],
                    ['name' => 'HYATT', 'type' => 'hotel', 'sort_order' => 6],
                ];

                foreach ($partners as $p) {
                    HajjPartner::create(array_merge($p, ['is_active' => true]));
                }
            }

            // 5. Seed Featured Learning Videos (hajj_videos)
            if (ModuleVideo::count() === 0) {
                $videos = [
                    [
                        'title'          => 'Learn Correctly How to Perform Umrah Step-by-Step',
                        'title_bn'       => 'সহীহ সুন্নাহ অনুযায়ী ওমরাহ পালনের সম্পূর্ণ নিয়ম শিখুন',
                        'title_ar'       => 'تعلم الطريقة الصحيحة لأداء مناسك العمرة خطوة بخطوة',
                        'description'    => 'Complete visual guide covering Ihram rules at Miqat, Talbiyah, 7 circuits of Tawaf around the Holy Kaaba, Maqam Ibrahim prayer, and Saee between Safa & Marwah.',
                        'description_bn' => 'মিকাত থেকে ইহরাম বাঁধা, তালবিয়াহ পাঠ, পবিত্র কাবা শরীফ তাওয়াফ, মাকামে ইবরাহীমে সালাত এবং সাফা-মারওয়া সাঈ করার পূর্ণাঙ্গ ভিডিও নির্দেশিকা।',
                        'description_ar' => 'دليل مرئي شامل يشرح أحكام الإحرام من الميقات والتلبية والطواف حول الكعبة المشرفة والصلاة خلف مقام إبراهيم والسعي بين الصفا والمروة.',
                        'category'       => 'Umrah Guide',
                        'video_url'      => 'https://www.youtube.com/embed/ScMzIvxBSi4',
                        'thumbnail'      => '/module-media/HajjUmrah/theme/offer-kaaba-door.jpg',
                        'duration'       => '14:25',
                        'sort_order'     => 1,
                    ],
                    [
                        'title'          => 'Complete 5-Day Hajj Rituals Guide (Mina, Arafat & Muzdalifah)',
                        'title_bn'       => 'হজ্জের ৫ দিনের পূর্ণাঙ্গ আমল ও নিয়মাবলী (মিনা, আরাফাত ও মুজদালিফা)',
                        'title_ar'       => 'دليل مناسك الحج الكامل (منى وعرفات ومزدلفة والجمرات)',
                        'description'    => 'Detailed walkthrough of the days of Tarwiyah, Wuquf at Arafat, spending the night in Muzdalifah, Rami al-Jamarat, Qurbani, and Tawaf al-Ifadah.',
                        'description_bn' => 'ইয়াওমুত তারবিয়াহ, আরাফাতের ময়দানে অবস্থান, মুজদালিফায় রাত্রিযাপন, জামারাতে কঙ্কর নিক্ষেপ, কুরবানী ও তাওয়াফে জিয়ারাতের বিস্তারিত গাইড।',
                        'description_ar' => 'شرح مفصل ليوم التروية والوقوف بعرفة والمبيت بمزدلفة ورمي الجمرات وطواف الإفاضة.',
                        'category'       => 'Hajj Guide',
                        'video_url'      => 'https://www.youtube.com/embed/ScMzIvxBSi4',
                        'thumbnail'      => '/module-media/HajjUmrah/theme/offer-mosque.jpg',
                        'duration'       => '19:50',
                        'sort_order'     => 2,
                    ],
                    [
                        'title'          => 'Essential Packing Checklist & Health Tips for Pilgrims',
                        'title_bn'       => 'হজ্জ ও ওমরাহ যাত্রীদের প্রয়োজনীয় প্রস্তুতি ও স্বাস্থ্য টিপস',
                        'title_ar'       => 'قائمة المستلزمات الضرورية والنصائح الصحية للحجاج والمعتمرين',
                        'description'    => 'What documents, Ihram garments, medications, and travel essentials to pack for a comfortable journey to Makkah and Madinah.',
                        'description_bn' => 'মক্কা ও মদিনা সফরের আগে কী কী কাগজপত্র, ইহরামের কাপড়, প্রয়োজনীয় ওষুধ এবং অনুষঙ্গ সাথে নেবেন তার তালিকা।',
                        'description_ar' => 'أهم الوثائق وملابس الإحرام والأدوية والمستلزمات الشخصية لرحلة مريحة إلى مكة والمدينة.',
                        'category'       => 'Preparation',
                        'video_url'      => 'https://www.youtube.com/embed/ScMzIvxBSi4',
                        'thumbnail'      => '/module-media/HajjUmrah/theme/offer-passport.jpg',
                        'duration'       => '08:40',
                        'sort_order'     => 3,
                    ],
                ];

                foreach ($videos as $v) {
                    ModuleVideo::create(array_merge($v, ['is_active' => true]));
                }
            }

            // 6. Seed Testimonials (hajj_testimonials)
            if (HajjTestimonial::count() === 0) {
                $testimonials = [
                    [
                        'client_name'    => 'Alhaj Abdul Karim',
                        'client_name_bn' => 'আলহাজ্ব আব্দুল করিম',
                        'client_name_ar' => 'الحاج عبد الكريم',
                        'designation'    => 'Standard Hajj Pilgrim, Dhaka',
                        'designation_bn' => 'স্ট্যান্ডার্ড হজ্জ হাজী, ঢাকা',
                        'designation_ar' => 'حاج الباقة القياسية، دكا',
                        'review_text'    => 'Alhamdulillah! Al Hajenra fulfilled every promise. Our Makkah hotel was only 350m from Haram and our Muallim guided us with extraordinary patience and care.',
                        'review_text_bn' => 'আলহামদুলিল্লাহ! আল হাজেনরা তাদের প্রতিটি প্রতিশ্রুতি রক্ষা করেছে। মক্কায় আমাদের হোটেল হারাম শরীফ থেকে মাত্র ৩৫০ মিটার দূরে ছিল এবং মুয়াল্লিম সাহেব অত্যন্ত যত্নের সাথে আমাদের গাইড করেছেন।',
                        'review_text_ar' => 'الحمد لله! أوفت وكالة الهجينرة بكل وعودها. كان فندقنا في مكة على بعد ٣٥٠ متراً فقط من الحرم المكي وكان المرشدون في قمة التعاون والرعاية.',
                        'rating'         => 5,
                        'avatar'         => '/module-media/HajjUmrah/theme/offer-family.jpg',
                        'sort_order'     => 1,
                    ],
                    [
                        'client_name'    => 'Dr. Mahmudul Hasan & Family',
                        'client_name_bn' => 'ডা. মাহমুদুল হাসান ও পরিবার',
                        'client_name_ar' => 'د. محمود الحسن وعائلته',
                        'designation'    => 'Family Umrah Package',
                        'designation_bn' => 'ফ্যামিলি ওমরাহ প্যাকেজ',
                        'designation_ar' => 'باقة العمرة العائلية',
                        'review_text'    => 'Traveling with elderly parents and children was completely stress-free thanks to Al Hajenra. From Jeddah airport pickup to Madinah Ziyarah, everything was first-class.',
                        'review_text_bn' => 'বয়স্ক বাবা-মা এবং সন্তানদের নিয়ে ওমরাহ সফর আল হাজেনরার কারণে সম্পূর্ণ ঝামেলামুক্ত হয়েছে। জেদ্দা এয়ারপোর্ট রিসিভ থেকে শুরু করে মদিনা জিয়ারাহ পর্যন্ত সব সেবা ছিল অসাধারণ।',
                        'review_text_ar' => 'السفر مع الوالدين والأطفال كان مريحاً للغاية بفضل الله ثم تنظيم الهجينرة الممتاز من مطار جدة إلى المدينة المنورة.',
                        'rating'         => 5,
                        'avatar'         => '/module-media/HajjUmrah/theme/offer-mosque.jpg',
                        'sort_order'     => 2,
                    ],
                    [
                        'client_name'    => 'Engr. Tariqul Islam',
                        'client_name_bn' => 'ইঞ্জিনিয়ার তারিকুল ইসলাম',
                        'client_name_ar' => 'المهندس طارق الإسلام',
                        'designation'    => 'Classic Hajj Package',
                        'designation_bn' => 'ক্লাসিক হজ্জ প্যাকেজ',
                        'designation_ar' => 'باقة الحج الكلاسيكية',
                        'review_text'    => 'Best Hajj agency in Bangladesh! Delicious halal Bangladeshi meals three times a day and 100m walking distance to Masjid Al-Haram made our worship so easy.',
                        'review_text_bn' => 'বাংলাদেশের সেরা হজ্জ এজেন্সি! প্রতিদিন ৩ বেলা সুস্বাদু দেশি খাবার এবং মসজিদে হারামের মাত্র ১০০ মিটার কাছে হোটেল হওয়ায় আমাদের ইবাদত অত্যন্ত সহজ হয়েছে।',
                        'review_text_ar' => 'أفضل وكالة حج في بنغلاديش! وجبات حلال شهية ثلاث مرات يومياً وفندق على بعد ١٠٠ متر فقط من المسجد الحرام.',
                        'rating'         => 5,
                        'avatar'         => '/module-media/HajjUmrah/theme/offer-kaaba-door.jpg',
                        'sort_order'     => 3,
                    ],
                ];

                foreach ($testimonials as $testi) {
                    HajjTestimonial::create(array_merge($testi, ['is_active' => true]));
                }
            }

            // 7. Seed Blog Articles (hajj_blogs)
            if (HajjBlog::count() === 0) {
                $blogs = [
                    [
                        'title'                => 'Step-by-Step Guide to Performing Your First Umrah',
                        'title_bn'             => 'প্রথমবার ওমরাহ পালনের ধাপে ধাপে পূর্ণাঙ্গ গাইড',
                        'title_ar'             => 'دليل خطوة بخطوة لأداء العمرة لأول مرة',
                        'slug'                 => 'step-by-step-guide-first-umrah',
                        'category'             => 'Umrah Guide',
                        'category_bn'          => 'ওমরাহ গাইড',
                        'category_ar'          => 'دليل العمرة',
                        'published_date'       => 'Nov 05, 2026',
                        'short_description'    => 'Learn the essential pillars of Umrah: Ihram, Tawaf around the Kaaba, Saee between Safa and Marwah, and Halq or Taqsir.',
                        'short_description_bn' => 'ওমরাহর ৪টি প্রধান রুকন সম্পর্কে জানুন: ইহরাম, পবিত্র কাবা তাওয়াফ, সাফা-মারওয়া সাঈ এবং হলক বা কসর।',
                        'short_description_ar' => 'تعرف على أركان العمرة الأساسية: الإحرام، الطواف حول الكعبة، السعي بين الصفا والمروة، والحلق أو التقصير.',
                        'content'              => '<p>Performing Umrah is one of the most spiritually rewarding journeys in a Muslim\'s life. Proper preparation ensures you can focus wholeheartedly on worship and supplication.</p>',
                        'content_bn'           => '<p>ওমরাহ পালন একজন মুসলিমের জীবনের অন্যতম সেরা আধ্যাত্মিক সফর। সঠিক নিয়ম জেনে প্রস্তুতি নিলে একগ্রচিত্তে ইবাদত ও দোয়া করা সহজ হয়।</p>',
                        'content_ar'           => '<p>أداء العمرة من أعظم الرحلات الإيمانية في حياة المسلم، والاستعداد الجيد يعين المعتمر على الخشوع والعبادة.</p>',
                        'image'                => '/module-media/HajjUmrah/theme/offer-kaaba-door.jpg',
                        'views'                => 1840,
                        'is_featured'          => true,
                    ],
                    [
                        'title'                => 'How to Choose the Right Hajj Package for Your Family',
                        'title_bn'             => 'আপনার পরিবারের জন্য সঠিক হজ্জ প্যাকেজ কীভাবে নির্বাচন করবেন',
                        'title_ar'             => 'كيف تختار باقة الحج المناسبة لعائلتك',
                        'slug'                 => 'how-to-choose-right-hajj-package',
                        'category'             => 'Hajj Preparation',
                        'category_bn'          => 'হজ্জ প্রস্তুতি',
                        'category_ar'          => 'الاستعداد للحج',
                        'published_date'       => 'Oct 28, 2026',
                        'short_description'    => 'Comparing Economy, Standard, and Classic Hajj packages: hotel distance from Haram, flight options, and Aziziyah facilities.',
                        'short_description_bn' => 'ইকোনমি, স্ট্যান্ডার্ড এবং ক্লাসিক হজ্জ প্যাকেজের তুলনা: হারাম শরীফ থেকে হোটেলের দূরত্ব, সরাসরি ফ্লাইট এবং আজিজিয়াহ সুবিধা।',
                        'short_description_ar' => 'مقارنة بين الباقات الاقتصادية والقياسية والكلاسيكية من حيث المسافة عن الحرم والطيران المباشر.',
                        'content'              => '<p>Selecting the right Hajj package depends on your physical comfort needs, hotel proximity to Masjid Al-Haram, and preferred flight schedule.</p>',
                        'content_bn'           => '<p>সঠিক হজ্জ প্যাকেজ নির্বাচন নির্ভর করে আপনার শারীরিক সক্ষমতা, মসজিদে হারাম থেকে হোটেলের দূরত্ব এবং ফ্লাইটের সময়সূচীর ওপর।</p>',
                        'content_ar'           => '<p>يعتمد اختيار باقة الحج المناسبة على احتياجات الراحة والمسافة إلى المسجد الحرام وجدول الرحلات المباشرة.</p>',
                        'image'                => '/module-media/HajjUmrah/theme/offer-family.jpg',
                        'views'                => 1320,
                        'is_featured'          => true,
                    ],
                    [
                        'title'                => 'Top Historical Ziyarah Places in Makkah and Madinah',
                        'title_bn'             => 'মক্কা ও মদিনার শীর্ষ ঐতিহাসিক জিয়ারাহ স্থানসমূহ',
                        'title_ar'             => 'أهم معالم الزيارة التاريخية في مكة المكرمة والمدينة المنورة',
                        'slug'                 => 'top-historical-ziyarah-places-makkah-madinah',
                        'category'             => 'Ziyarah Tour',
                        'category_bn'          => 'জিয়ারাহ ট্যুর',
                        'category_ar'          => 'جولات الزيارة',
                        'published_date'       => 'Oct 15, 2026',
                        'short_description'    => 'Explore Jabal al-Noor, Cave of Thawr, Mount Uhud, Masjid Quba, and Masjid al-Qiblatayn with our scholarly tour guides.',
                        'short_description_bn' => 'আমাদের অভিজ্ঞ আলেম গাইডদের সাথে জাবালে নূর, গারে সওর, উহুদ পাহাড়, মসজিদে কুবা এবং মসজিদে কিবলাতাইন জিয়ারাহ করুন।',
                        'short_description_ar' => 'استكشف جبل النور وغار ثور وجبل أحد ومسجد قباء ومسجد القبلتين مع مرشدينا المختصين.',
                        'content'              => '<p>Visiting the historical landmarks of Islam in Makkah and Madinah deepens your connection to the Seerah of Prophet Muhammad (PBUH).</p>',
                        'content_bn'           => '<p>মক্কা ও মদিনার ঐতিহাসিক ইসলামিক স্থানগুলো পরিদর্শন রাসূলুল্লাহ (সা.)-এর সীরাতের সাথে আপনার আত্মিক সম্পর্ককে আরও গভীর করে।</p>',
                        'content_ar'           => '<p>إن زيارة المعالم التاريخية الإسلامية في مكة والمدينة تعمق ارتباطك بالسيرة النبوية العطرة.</p>',
                        'image'                => '/module-media/HajjUmrah/theme/offer-mosque.jpg',
                        'views'                => 1590,
                        'is_featured'          => true,
                    ],
                ];

                foreach ($blogs as $b) {
                    HajjBlog::create(array_merge($b, ['is_active' => true]));
                }
            }

            // 8. Seed Gallery Media (hajj_media)
            if (ModuleMedia::count() === 0) {
                $mediaItems = [
                    ['title' => 'Holy Kaaba & Masjid Al-Haram Archways', 'title_bn' => 'পবিত্র কাবা ও মসজিদে হারাম', 'title_ar' => 'الكعبة المشرفة وأروقة المسجد الحرام', 'category' => 'makkah', 'file_path' => '/module-media/HajjUmrah/theme/hero-kaaba-haram.jpg', 'alt_text' => 'Holy Kaaba'],
                    ['title' => 'Golden Door of Kaaba & Tawaf Pilgrims', 'title_bn' => 'কাবা শরীফের স্বর্ণের দরজা ও তাওয়াফ', 'title_ar' => 'باب الكعبة الذهبي والطائفين', 'category' => 'makkah', 'file_path' => '/module-media/HajjUmrah/theme/offer-kaaba-door.jpg', 'alt_text' => 'Kaaba Door'],
                    ['title' => 'Masjid An-Nabawi Courtyard & Pilgrims', 'title_bn' => 'মসজিদে নববীর প্রাঙ্গণ', 'title_ar' => 'رحاب المسجد النبوي الشريف', 'category' => 'madinah', 'file_path' => '/module-media/HajjUmrah/theme/offer-mosque.jpg', 'alt_text' => 'Masjid An-Nabawi'],
                    ['title' => 'Family Pilgrims in Ihram Attire', 'title_bn' => 'ইহরাম পরিহিত হজ্জ পরিবার', 'title_ar' => 'عائلة بملابس الإحرام', 'category' => 'pilgrims', 'file_path' => '/module-media/HajjUmrah/theme/offer-family.jpg', 'alt_text' => 'Family Umrah'],
                    ['title' => 'Hajj Passport & Direct Flight Boarding Pass', 'title_bn' => 'হজ্জ পাসপোর্ট ও বিমান টিকেট', 'title_ar' => 'جواز السفر وتذكرة رحلة الحج', 'category' => 'flights', 'file_path' => '/module-media/HajjUmrah/theme/offer-passport.jpg', 'alt_text' => 'Passport and Boarding Pass'],
                    ['title' => 'Night Illumination at Masjid Al-Haram', 'title_bn' => 'রাতে মসজিদে হারামের আলোকসজ্জা', 'title_ar' => 'الكعبة المشرفة ليلاً', 'category' => 'makkah', 'file_path' => '/module-media/HajjUmrah/theme/footer-kaaba-night.jpg', 'alt_text' => 'Kaaba Night'],
                ];

                foreach ($mediaItems as $m) {
                    ModuleMedia::create(array_merge($m, [
                        'file_type' => 'image/jpeg',
                        'file_size' => '210 KB',
                    ]));
                }
            }

            // 9. Seed Menus (hajj_menus)
            if (ModuleMenu::count() === 0) {
                $menus = [
                    ['title' => 'Home', 'title_bn' => 'হোম', 'title_ar' => 'الرئيسية', 'url' => '#home', 'location' => 'header', 'sort_order' => 1],
                    ['title' => 'Hajj', 'title_bn' => 'হজ্জ', 'title_ar' => 'الحج', 'url' => '#special-offers', 'location' => 'header', 'sort_order' => 2],
                    ['title' => 'Umrah', 'title_bn' => 'ওমরাহ', 'title_ar' => 'العمرة', 'url' => '#special-offers', 'location' => 'header', 'sort_order' => 3],
                    ['title' => 'Facilities', 'title_bn' => 'সুবিধাসমূহ', 'title_ar' => 'الخدمات', 'url' => '#services', 'location' => 'header', 'sort_order' => 4],
                    ['title' => 'Tour', 'title_bn' => 'প্যাকেজ ট্যুর', 'title_ar' => 'الباقات', 'url' => '#pricing', 'location' => 'header', 'sort_order' => 5],
                    ['title' => 'Blog', 'title_bn' => 'ব্লগ', 'title_ar' => 'المدونة', 'url' => '#blog', 'location' => 'header', 'sort_order' => 6],
                    ['title' => 'Gallery', 'title_bn' => 'গ্যালারি', 'title_ar' => 'المعرض', 'url' => '#gallery', 'location' => 'header', 'sort_order' => 7],

                    // Footer Quick Links
                    ['title' => 'Home', 'title_bn' => 'হোম', 'title_ar' => 'الرئيسية', 'url' => '#home', 'location' => 'footer', 'sort_order' => 1],
                    ['title' => 'Hajj', 'title_bn' => 'হজ্জ প্যাকেজ', 'title_ar' => 'الحج', 'url' => '#special-offers', 'location' => 'footer', 'sort_order' => 2],
                    ['title' => 'Umrah', 'title_bn' => 'ওমরাহ প্যাকেজ', 'title_ar' => 'العمرة', 'url' => '#special-offers', 'location' => 'footer', 'sort_order' => 3],
                    ['title' => 'Packages', 'title_bn' => 'মূল্য তালিকা', 'title_ar' => 'الباقات والأسعار', 'url' => '#pricing', 'location' => 'footer', 'sort_order' => 4],
                    ['title' => 'Contact', 'title_bn' => 'যোগাযোগ', 'title_ar' => 'اتصل بنا', 'url' => '#contact', 'location' => 'footer', 'sort_order' => 5],
                ];

                foreach ($menus as $m) {
                    ModuleMenu::create(array_merge($m, ['target' => '_self', 'is_active' => true]));
                }
            }

            // 10. Seed Standard CMS Pages, Sliders, Themes, Users, Sample Inquiry & Subscriber
            if (ModulePage::count() === 0) {
                ModulePage::create([
                    'title'    => 'Al Hajenra Hajj & Umrah Home',
                    'title_bn' => 'আল হাজেনরা হজ্জ ও ওমরাহ হোম',
                    'title_ar' => 'الصفحة الرئيسية - الهجينرة للحج والعمرة',
                    'slug'     => 'home',
                    'content'  => 'Economy & Luxury Hajj and Umrah Packages.',
                    'status'   => 'Published',
                    'views'    => 3420,
                ]);
                ModulePage::create([
                    'title'    => '2028 Hajj Registration & Visa Guidelines',
                    'title_bn' => '২০২৮ হজ্জ প্রাক-নিবন্ধন ও ভিসা নির্দেশিকা',
                    'title_ar' => 'إرشادات التسجيل وتأشيرة الحج ٢٠٢٨',
                    'slug'     => 'hajj-registration-guidelines',
                    'content'  => 'Official requirements for Saudi Ministry of Hajj & Umrah Nusantara / Nusuk registration.',
                    'status'   => 'Published',
                    'views'    => 1890,
                ]);
            }

            if (ModuleSlider::count() === 0) {
                ModuleSlider::create([
                    'title'          => 'Economy Hajj & Umrah Package',
                    'title_bn'       => 'ইকোনমি হজ্জ ও ওমরাহ প্যাকেজ',
                    'title_ar'       => 'باقات الحج والعمرة الاقتصادية',
                    'subtitle'       => 'Budget-friendly & luxury package for a comfortable hajj and umrah journey to the holy makah & madinah for muslim brothers and sisters.',
                    'subtitle_bn'    => 'পবিত্র মক্কা ও মদিনায় মুসলিম ভাই ও বোনদের আরামদায়ক হজ্জ ও ওমরাহ সফরের জন্য সাশ্রয়ী ও বিলাসবহুল প্যাকেজ।',
                    'subtitle_ar'    => 'باقات اقتصادية وفاخرة لرحلة حج وعمرة مريحة إلى مكة المكرمة والمدينة المنورة.',
                    'button_text'    => 'Get a Package Price',
                    'button_text_bn' => 'প্যাকেজ মূল্য জানুন',
                    'button_text_ar' => 'احصل على سعر الباقة',
                    'button_url'     => '#pricing',
                    'image'          => '/module-media/HajjUmrah/theme/hero-makkah-tawaf.jpg',
                    'sort_order'     => 1,
                    'is_active'      => true,
                ]);
                ModuleSlider::create([
                    'title'          => 'Economy Hajj & Umrah Package',
                    'title_bn'       => 'ইকোনমি হজ্জ ও ওমরাহ প্যাকেজ',
                    'title_ar'       => 'باقات الحج والعمرة الاقتصادية',
                    'subtitle'       => 'Budget-friendly & luxury package for a comfortable hajj and umrah journey to the holy makah & madinah for muslim brothers and sisters.',
                    'subtitle_bn'    => 'পবিত্র মক্কা ও মদিনায় মুসলিম ভাই ও বোনদের আরামদায়ক হজ্জ ও ওমরাহ সফরের জন্য সাশ্রয়ী ও বিলাসবহুল প্যাকেজ।',
                    'subtitle_ar'    => 'باقات اقتصادية وفاخرة لرحلة حج وعمرة مريحة إلى مكة المكرمة والمدينة المنورة.',
                    'button_text'    => 'Get a Package Price',
                    'button_text_bn' => 'প্যাকেজ মূল্য জানুন',
                    'button_text_ar' => 'احصل على سعر الباقة',
                    'button_url'     => '#pricing',
                    'image'          => '/module-media/HajjUmrah/theme/hero-kaaba-haram.jpg',
                    'sort_order'     => 2,
                    'is_active'      => true,
                ]);
            }

            if (ModuleTheme::count() === 0) {
                ModuleTheme::create([
                    'name'            => 'Al Hajenra Kaaba Royal Gold & Emerald (Default)',
                    'slug'            => 'al-hajenra-kaaba-royal-emerald',
                    'primary_color'   => '#15803D',
                    'secondary_color' => '#080B0A',
                    'font_family'     => 'Plus Jakarta Sans, Hind Siliguri & Cairo',
                    'layout_style'    => 'Islamic Kaaba Hero + Emerald Pricing Cards',
                    'thumbnail'       => '/module-media/HajjUmrah/theme/hero-kaaba-haram.jpg',
                    'is_active'       => true,
                ]);
                ModuleTheme::create([
                    'name'            => 'Madinah Rawdah Deep Green & Warm Gold',
                    'slug'            => 'madinah-rawdah-emerald-gold',
                    'primary_color'   => '#045D44',
                    'secondary_color' => '#051B14',
                    'font_family'     => 'Playfair Display, Hind Siliguri & Cairo',
                    'layout_style'    => 'RoyalHaramain Classic Layout',
                    'thumbnail'       => '/module-media/HajjUmrah/theme/offer-mosque.jpg',
                    'is_active'       => false,
                ]);
            }

            if (ModuleUser::count() === 0) {
                ModuleUser::create([
                    'name'      => 'Maulana Habibur Rahman (Chief Muallim)',
                    'email'     => 'muallim@alhajenra.com',
                    'phone'     => '+880 1711-998877',
                    'role_name' => 'Administrator',
                    'status'    => 'Active',
                ]);
                ModuleUser::create([
                    'name'      => 'Zubair Al-Mansoor (Visa & Ticketing Head)',
                    'email'     => 'visa@alhajenra.com',
                    'phone'     => '+880 1819-554433',
                    'role_name' => 'Manager',
                    'status'    => 'Active',
                ]);
            }

            if (HajjInquiry::count() === 0) {
                HajjInquiry::create([
                    'name'            => 'Alhaj Mohammad Siddique',
                    'email'           => 'siddique@example.com',
                    'phone'           => '+880 1712-345678',
                    'package_name'    => 'Standard Package (BDT 7,35,000)',
                    'travelers_count' => '2 Persons (Couple)',
                    'preferred_date'  => 'Hajj Season 2028',
                    'message'         => 'Assalamu Alaikum, we would like to book the Standard Package for 2 persons with a double room near Masjid Al-Haram.',
                    'status'          => 'Unread',
                ]);
            }

            if (HajjSubscriber::count() === 0) {
                HajjSubscriber::create([
                    'email'  => 'pilgrim@alhajenra.com',
                    'status' => 'Subscribed',
                ]);
            }
        } catch (\Throwable $e) {
            // Fail gracefully if migrations are not yet run
        }
    }
}
