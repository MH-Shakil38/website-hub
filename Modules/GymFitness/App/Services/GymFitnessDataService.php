<?php

namespace Modules\GymFitness\App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\GymFitness\App\Models\GymAttendance;
use Modules\GymFitness\App\Models\GymClass;
use Modules\GymFitness\App\Models\GymClassBooking;
use Modules\GymFitness\App\Models\GymEquipment;
use Modules\GymFitness\App\Models\GymFaq;
use Modules\GymFitness\App\Models\GymFitnessPlan;
use Modules\GymFitness\App\Models\GymMember;
use Modules\GymFitness\App\Models\GymPackage;
use Modules\GymFitness\App\Models\GymPayment;
use Modules\GymFitness\App\Models\GymProgram;
use Modules\GymFitness\App\Models\GymProgressLog;
use Modules\GymFitness\App\Models\GymSubscription;
use Modules\GymFitness\App\Models\GymTestimonial;
use Modules\GymFitness\App\Models\GymTrainer;
use Modules\GymFitness\App\Models\ModuleMedia;
use Modules\GymFitness\App\Models\ModuleMenu;
use Modules\GymFitness\App\Models\ModulePage;
use Modules\GymFitness\App\Models\ModuleSetting;
use Modules\GymFitness\App\Models\ModuleSlider;
use Modules\GymFitness\App\Models\ModuleTheme;
use Modules\GymFitness\App\Models\ModuleUser;
use Modules\GymFitness\App\Models\ModuleVideo;

class GymFitnessDataService
{
    /**
     * Get merged settings & section content from Config/defaults.php + gym_settings overrides.
     */
    public function getSettingsData(): array
    {
        $defaults = require module_path('GymFitness', 'Config/defaults.php');

        try {
            if (!Schema::connection('tenant')->hasTable('gym_settings')) {
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
     * Record a gym payment in the global core_transactions table as well so global accounting stays unified.
     */
    public function syncWithCoreTransactions(GymPayment $payment, string $phone): void
    {
        try {
            if (!Schema::hasTable('core_transactions') || $payment->payment_status !== 'Paid') {
                return;
            }

            $txnId = $payment->transaction_reference ?: ('GYM-' . $payment->invoice_no);
            Transaction::updateOrCreate(
                ['transaction_id' => $txnId],
                [
                    'transacted_at'   => $payment->paid_at ?? now(),
                    'wallet_number'   => $phone ?: '01700-000000',
                    'amount'          => $payment->amount,
                    'coupon_amount'   => $payment->discount_amount ?? 0,
                    'net_amount'      => $payment->paid_amount,
                    'cashback_amount' => 0,
                    'type'            => 'Payment',
                    'channel'         => $payment->payment_method ?: 'PGW',
                ]
            );
        } catch (\Throwable $e) {
            // Ignore if core_transactions is unavailable
        }
    }

    /**
     * Ensure all gym_* tables are seeded with reference data if empty.
     */
    public function ensureSeeded(): void
    {
        try {
            if (!Schema::connection('tenant')->hasTable('gym_packages')) {
                return;
            }

            // 1. Seed 3 Membership Packages (Exact match to Reference Image: Basic $19, Premium $39 Most Popular, Elite $59)
            if (GymPackage::count() === 0) {
                $packages = [
                    [
                        'name'                  => 'Basic Plan',
                        'name_bn'               => 'বেসিক প্ল্যান',
                        'slug'                  => 'basic-plan',
                        'subtitle'              => 'Perfect for beginners',
                        'subtitle_bn'           => 'নতুনদের জন্য আদর্শ প্যাকেজ',
                        'description'           => 'Full access to our gym floor, cardio & strength zones, and personal locker facility.',
                        'description_bn'        => 'আমাদের জিম ফ্লোর, কার্ডিও ও স্ট্রেংথ জোন এবং ব্যক্তিগত লকার সুবিধায় পূর্ণ প্রবেশাধিকার।',
                        'currency'              => '$',
                        'monthly_price'         => 19.00,
                        'quarterly_price'       => 49.00,
                        'six_month_price'       => 89.00,
                        'yearly_price'          => 159.00,
                        'custom_duration_label' => '15 Days Trial',
                        'custom_duration_price' => 12.00,
                        'registration_fee'      => 5.00,
                        'features'              => ['Gym Access', 'Cardio & Weight Area', 'Locker Room Access'],
                        'features_bn'           => ['জিম অ্যাক্সেস', 'কার্ডিও ও ওয়েট এরিয়া', 'লকার রুম অ্যাক্সেস'],
                        'has_personal_trainer'  => false,
                        'has_group_classes'     => false,
                        'has_locker'            => true,
                        'has_nutrition'         => false,
                        'badge_text'            => null,
                        'badge_text_bn'         => null,
                        'button_text'           => 'Get Started',
                        'button_text_bn'        => 'শুরু করুন',
                        'is_featured'           => false,
                        'sort_order'            => 1,
                    ],
                    [
                        'name'                  => 'Premium Plan',
                        'name_bn'               => 'প্রিমিয়াম প্ল্যান',
                        'slug'                  => 'premium-plan',
                        'subtitle'              => 'For serious fitness lovers',
                        'subtitle_bn'           => 'নিয়মিত ফিটনেস অনুশীলনকারীদের জন্য',
                        'description'           => 'Includes dedicated Personal Trainer guidance, unlimited group fitness classes, locker & towel service.',
                        'description_bn'        => 'এর মধ্যে রয়েছে ডেডিকেটেড পার্সোনাল ট্রেইনার গাইডেন্স, আনলিমিটেড গ্রুপ ক্লাস এবং লকার ও টাওয়েল সার্ভিস।',
                        'currency'              => '$',
                        'monthly_price'         => 39.00,
                        'quarterly_price'       => 99.00,
                        'six_month_price'       => 179.00,
                        'yearly_price'          => 299.00,
                        'custom_duration_label' => '2 Months Transformation',
                        'custom_duration_price' => 70.00,
                        'registration_fee'      => 0.00,
                        'features'              => ['Full Gym Access', 'All Group Classes', '1 Free Trainer Session', 'Diet Guide'],
                        'features_bn'           => ['পূর্ণ জিম অ্যাক্সেস', 'সকল গ্রুপ ক্লাস', '১টি ফ্রি ট্রেইনার সেশন', 'ডায়েট গাইড'],
                        'has_personal_trainer'  => true,
                        'has_group_classes'     => true,
                        'has_locker'            => true,
                        'has_nutrition'         => false,
                        'badge_text'            => 'Most Popular',
                        'badge_text_bn'         => 'সবচেয়ে জনপ্রিয়',
                        'button_text'           => 'Join Now',
                        'button_text_bn'        => 'জয়েন করুন',
                        'is_featured'           => true,
                        'sort_order'            => 2,
                    ],
                    [
                        'name'                  => 'Elite Plan',
                        'name_bn'               => 'এলিট প্ল্যান',
                        'slug'                  => 'elite-plan',
                        'subtitle'              => 'For the ultimate results',
                        'subtitle_bn'           => 'সর্বোচ্চ ফিটনেস ফলাফলের জন্য ভিআইপি প্ল্যান',
                        'description'           => 'All-inclusive VIP membership with 1-on-1 personal training, custom nutrition & diet plan, and priority support.',
                        'description_bn'        => '১-অন-১ পার্সোনাল ট্রেনিং, কাস্টম নিউট্রিশন ও ডায়েট প্ল্যান এবং ভিআইপি প্রায়োরিটি সাপোর্টসহ পূর্ণাঙ্গ মেম্বারশিপ।',
                        'currency'              => '$',
                        'monthly_price'         => 59.00,
                        'quarterly_price'       => 149.00,
                        'six_month_price'       => 269.00,
                        'yearly_price'          => 449.00,
                        'custom_duration_label' => '90 Days Body Recomp',
                        'custom_duration_price' => 149.00,
                        'registration_fee'      => 0.00,
                        'features'              => ['24/7 Gym Access', 'Personal Trainer', 'Custom Nutrition Plan', 'Priority Support'],
                        'features_bn'           => ['২৪/৭ জিম অ্যাক্সেস', 'পার্সোনাল ট্রেইনার', 'কাস্টম নিউট্রিশন প্ল্যান', 'প্রায়োরিটি সাপোর্ট'],
                        'has_personal_trainer'  => true,
                        'has_group_classes'     => true,
                        'has_locker'            => true,
                        'has_nutrition'         => true,
                        'badge_text'            => 'VIP Access',
                        'badge_text_bn'         => 'ভিআইপি প্ল্যান',
                        'button_text'           => 'Get Started',
                        'button_text_bn'        => 'শুরু করুন',
                        'is_featured'           => false,
                        'sort_order'            => 3,
                    ],
                ];

                foreach ($packages as $pkg) {
                    GymPackage::create(array_merge($pkg, ['is_active' => true]));
                }
            }

            // 2. Seed 5 Training Programs (Exact match to Reference Image)
            if (GymProgram::count() === 0) {
                $programs = [
                    [
                        'title'          => 'Weight Training',
                        'title_bn'       => 'ওয়েট ট্রেনিং',
                        'slug'           => 'weight-training',
                        'subtitle'       => 'Build muscle and strength with guided resistance programs.',
                        'subtitle_bn'    => 'পেশিশক্তি ও বডি বিল্ডিংয়ের গাইডেড রেজিস্ট্যান্স প্রোগ্রাম।',
                        'description'    => 'Progressive overload resistance training with Olympic barbells, dumbbells, and Hammer Strength machines.',
                        'description_bn' => 'অলিম্পিক বারবেল, ডাম্বেল এবং হ্যামার স্ট্রেংথ মেশিনের সাহায্যে পেশিশক্তি বৃদ্ধির আধুনিক অনুশীলন।',
                        'image'          => '/module-media/GymFitness/theme/prog-weight.jpg',
                        'icon_class'     => 'fa-solid fa-dumbbell',
                        'duration_label' => '60 Mins',
                        'calories_burn'  => '550 kcal',
                        'sort_order'     => 1,
                    ],
                    [
                        'title'          => 'Cardio & HIIT',
                        'title_bn'       => 'কার্ডিও ও HIIT',
                        'slug'           => 'cardio-fitness',
                        'subtitle'       => 'High-energy workouts designed to burn fat fast.',
                        'subtitle_bn'    => 'দ্রুত ফ্যাট বার্ন করার জন্য হাই-এনার্জি ওয়ার্কআউট।',
                        'description'    => 'High-energy treadmill intervals, rowing, air bikes, and endurance circuits for rapid fat burn and heart health.',
                        'description_bn' => 'দ্রুত ফ্যাট বার্ন এবং হৃদযন্ত্রের সক্ষমতা বাড়াতে ট্রেডমিল, রোয়িং ও কার্ডিও সার্কিট অনুশীলন।',
                        'image'          => '/module-media/GymFitness/theme/prog-cardio.jpg',
                        'icon_class'     => 'fa-solid fa-heart-pulse',
                        'duration_label' => '45 Mins',
                        'calories_burn'  => '650 kcal',
                        'sort_order'     => 2,
                    ],
                    [
                        'title'          => 'Personal Training',
                        'title_bn'       => 'পার্সোনাল ট্রেনিং',
                        'slug'           => 'personal-training',
                        'subtitle'       => '1-on-1 coaching tailored to your fitness goals.',
                        'subtitle_bn'    => 'আপনার লক্ষ্য অনুযায়ী ১-অন-১ বিশেষজ্ঞ কোচিং।',
                        'description'    => '1-on-1 customized coaching, weekly body composition tracking, and tailored nutrition planning with a master trainer.',
                        'description_bn' => 'অভিজ্ঞ মাস্টার ট্রেইনারের সাথে ১-অন-১ কাস্টমাইজড ওয়ার্কআউট এবং সাপ্তাহিক বডি মেজারমেন্ট ট্র্যাকিং।',
                        'image'          => '/module-media/GymFitness/theme/prog-personal.jpg',
                        'icon_class'     => 'fa-solid fa-user-check',
                        'duration_label' => '60 Mins',
                        'calories_burn'  => '600 kcal',
                        'sort_order'     => 3,
                    ],
                    [
                        'title'          => 'Group Classes',
                        'title_bn'       => 'গ্রুপ ক্লাস',
                        'slug'           => 'crossfit',
                        'subtitle'       => 'Fun, high-energy sessions led by expert instructors.',
                        'subtitle_bn'    => 'বিশেষজ্ঞ ইন্সট্রাক্টরদের সাথে প্রাণবন্ত গ্রুপ সেশন।',
                        'description'    => 'Functional high-intensity workouts combining kettlebells, battle ropes, box jumps, and metabolic conditioning.',
                        'description_bn' => 'কেটলবেল, ব্যাটল রোপ এবং বক্স জাম্পের সমন্বয়ে ফাংশনাল হাই-ইনটেনসিটি ক্রসফিট ওয়ার্কআউট।',
                        'image'          => '/module-media/GymFitness/theme/prog-crossfit.jpg',
                        'icon_class'     => 'fa-solid fa-fire-flame-curved',
                        'duration_label' => '50 Mins',
                        'calories_burn'  => '750 kcal',
                        'sort_order'     => 4,
                    ],
                    [
                        'title'          => 'Yoga & Mobility',
                        'title_bn'       => 'ইয়োগা ও মোবিলিটি',
                        'slug'           => 'yoga-flexibility',
                        'subtitle'       => 'Improve flexibility, posture, and recovery.',
                        'subtitle_bn'    => 'শারীরিক নমনীয়তা, অঙ্গবিন্যাস ও রিকভারি উন্নত করুন।',
                        'description'    => 'Guided mobility, posture correction, breathwork, and dynamic stretching sessions for recovery and core stability.',
                        'description_bn' => 'শরীরের নমনীয়তা বৃদ্ধি, সঠিক অঙ্গবিন্যাস ও মানসিক প্রশান্তির জন্য বিশেষ ইয়োগা সেশন।',
                        'image'          => '/module-media/GymFitness/theme/prog-yoga.jpg',
                        'icon_class'     => 'fa-solid fa-spa',
                        'duration_label' => '60 Mins',
                        'calories_burn'  => '350 kcal',
                        'sort_order'     => 5,
                    ],
                ];

                foreach ($programs as $prog) {
                    GymProgram::create(array_merge($prog, ['is_active' => true]));
                }
            }

            // 3. Seed 4 Expert Trainers (Exact match to Reference Image)
            if (GymTrainer::count() === 0) {
                $trainers = [
                    [
                        'name'              => 'Marvin McKinney',
                        'name_bn'           => 'মার্ভিন ম্যাককিনি',
                        'slug'              => 'marvin-mckinney',
                        'specialization'    => 'Strength & Conditioning',
                        'specialization_bn' => 'স্ট্রেংথ ও কন্ডিশনিং',
                        'experience'        => '8 Years',
                        'experience_bn'     => '৮ বছর',
                        'phone'             => '+880 1711-223344',
                        'email'             => 'marvin@fitpulse.com',
                        'photo'             => '/module-media/GymFitness/theme/trainer-1.jpg',
                        'bio'               => 'ISSA Certified Master Strength Coach specializing in hypertrophy, powerlifting, and athletic conditioning.',
                        'bio_bn'            => 'হাইপারট্রফি, পাওয়ারলিফটিং এবং অ্যাথলেটিক কন্ডিশনিংয়ে বিশেষজ্ঞ সার্টিফাইড মাস্টার স্ট্রেংথ কোচ।',
                        'availability'      => 'Mon - Sat (6:00 AM - 2:00 PM)',
                        'schedule_summary'  => 'Morning & Midday Strength Sessions',
                        'salary'            => 45000,
                        'commission_rate'   => 15.00,
                        'facebook_url'      => 'https://facebook.com',
                        'instagram_url'     => 'https://instagram.com',
                        'youtube_url'       => 'https://youtube.com',
                        'sort_order'        => 1,
                    ],
                    [
                        'name'              => 'Jacob Jones',
                        'name_bn'           => 'জ্যাকব জোনস',
                        'slug'              => 'jacob-jones',
                        'specialization'    => 'Strength & Conditioning',
                        'specialization_bn' => 'স্ট্রেংথ ও কন্ডিশনিং',
                        'experience'        => '5 Years',
                        'experience_bn'     => '৫ বছর',
                        'phone'             => '+880 1812-334455',
                        'email'             => 'jacob@fitpulse.com',
                        'photo'             => '/module-media/GymFitness/theme/trainer-2.jpg',
                        'bio'               => 'CrossFit Level-2 Trainer focused on high-intensity metabolic conditioning, agility, and rapid fat loss.',
                        'bio_bn'            => 'হাই-ইনটেনসিটি মেটাবলিক কন্ডিশনিং এবং দ্রুত ফ্যাট লস প্রোগ্রামে বিশেষজ্ঞ ক্রসফিট লেভেল-২ কোচ।',
                        'availability'      => 'Mon - Sat (3:00 PM - 10:00 PM)',
                        'schedule_summary'  => 'Evening CrossFit & HIIT Batches',
                        'salary'            => 40000,
                        'commission_rate'   => 12.00,
                        'facebook_url'      => 'https://facebook.com',
                        'instagram_url'     => 'https://instagram.com',
                        'youtube_url'       => 'https://youtube.com',
                        'sort_order'        => 2,
                    ],
                    [
                        'name'              => 'Arlene McCoy',
                        'name_bn'           => 'আরলিন ম্যাককয়',
                        'slug'              => 'arlene-mccoy',
                        'specialization'    => 'Strength & Conditioning',
                        'specialization_bn' => 'স্ট্রেংথ ও কন্ডিশনিং',
                        'experience'        => '2 Years',
                        'experience_bn'     => '২ বছর',
                        'phone'             => '+880 1911-445566',
                        'email'             => 'arlene@fitpulse.com',
                        'photo'             => '/module-media/GymFitness/theme/trainer-3.jpg',
                        'bio'               => 'Certified Personal Transformation Specialist helping members build lean muscle, strength, and posture.',
                        'bio_bn'            => 'কোর স্ট্রেংথ, মাসল বিল্ডিং এবং সঠিক অঙ্গবিন্যাস উন্নয়নে অভিজ্ঞ পার্সোনাল ট্রেনিং বিশেষজ্ঞ।',
                        'availability'      => 'Sun - Fri (7:00 AM - 6:00 PM)',
                        'schedule_summary'  => 'Morning & Evening Personal Coaching',
                        'salary'            => 38000,
                        'commission_rate'   => 10.00,
                        'facebook_url'      => 'https://facebook.com',
                        'instagram_url'     => 'https://instagram.com',
                        'youtube_url'       => 'https://youtube.com',
                        'sort_order'        => 3,
                    ],
                    [
                        'name'              => 'Zahin Hasan',
                        'name_bn'           => 'জাহিন হাসান',
                        'slug'              => 'zahin-hasan',
                        'specialization'    => 'Personal Trainer',
                        'specialization_bn' => 'পার্সোনাল ও নিউট্রিশন ট্রেইনার',
                        'experience'        => '7+ Years Exp',
                        'experience_bn'     => '৭+ বছরের অভিজ্ঞতা',
                        'phone'             => '+880 1611-556677',
                        'email'             => 'zahin@fitpulse.com',
                        'photo'             => '/module-media/GymFitness/theme/trainer-4.jpg',
                        'bio'               => 'Certified Sports Nutritionist and Personal Transformation Coach with 300+ successful client transformations.',
                        'bio_bn'            => '৩০০+ মেম্বারের সফল বডি ট্রান্সফরমেশনে নেতৃত্ব দেওয়া সার্টিফাইড স্পোর্টস নিউট্রিশনিস্ট ও পার্সোনাল কোচ।',
                        'availability'      => 'Mon - Sat (8:00 AM - 9:00 PM)',
                        'schedule_summary'  => '1-on-1 Personal Coaching & Diet',
                        'salary'            => 42000,
                        'commission_rate'   => 15.00,
                        'facebook_url'      => 'https://facebook.com',
                        'instagram_url'     => 'https://instagram.com',
                        'youtube_url'       => 'https://youtube.com',
                        'sort_order'        => 4,
                    ],
                ];

                foreach ($trainers as $tr) {
                    GymTrainer::create(array_merge($tr, ['is_active' => true]));
                }
            }

            // 4. Seed Weekly Class Schedule (Exact match to Reference Image Table)
            if (GymClass::count() === 0) {
                $t1 = GymTrainer::first()?->id;
                $t2 = GymTrainer::skip(1)->first()?->id ?? $t1;
                $t3 = GymTrainer::skip(2)->first()?->id ?? $t1;
                $t4 = GymTrainer::skip(3)->first()?->id ?? $t1;

                $scheduleRows = [
                    // 6:00 AM - 7:00 AM
                    ['time_slot' => '6:00 AM - 7:00 AM', 'day_of_week' => 'Monday',    'title' => 'Yoga',     'title_bn' => 'ইয়োগা',     'category' => 'Yoga',            'trainer_id' => $t3, 'sort_order' => 1],
                    ['time_slot' => '6:00 AM - 7:00 AM', 'day_of_week' => 'Tuesday',   'title' => 'Strength', 'title_bn' => 'স্ট্রেংথ',  'category' => 'Strength',        'trainer_id' => $t1, 'sort_order' => 2],
                    ['time_slot' => '6:00 AM - 7:00 AM', 'day_of_week' => 'Wednesday', 'title' => 'Cardio',   'title_bn' => 'কার্ডিও',   'category' => 'Cardio',          'trainer_id' => $t4, 'sort_order' => 3],
                    ['time_slot' => '6:00 AM - 7:00 AM', 'day_of_week' => 'Thursday',  'title' => 'Strength', 'title_bn' => 'স্ট্রেংথ',  'category' => 'Strength',        'trainer_id' => $t1, 'sort_order' => 4],
                    ['time_slot' => '6:00 AM - 7:00 AM', 'day_of_week' => 'Friday',    'title' => 'CrossFit', 'title_bn' => 'ক্রসফিট',   'category' => 'CrossFit',        'trainer_id' => $t2, 'sort_order' => 5],
                    ['time_slot' => '6:00 AM - 7:00 AM', 'day_of_week' => 'Saturday',  'title' => 'Cardio',   'title_bn' => 'কার্ডিও',   'category' => 'Cardio',          'trainer_id' => $t4, 'sort_order' => 6],
                    ['time_slot' => '6:00 AM - 7:00 AM', 'day_of_week' => 'Sunday',    'title' => 'Rest',     'title_bn' => 'বিশ্রাম',   'category' => 'Rest',            'trainer_id' => null, 'sort_order' => 7],

                    // 8:00 AM - 9:00 AM
                    ['time_slot' => '8:00 AM - 9:00 AM', 'day_of_week' => 'Monday',    'title' => 'Strength', 'title_bn' => 'স্ট্রেংথ',  'category' => 'Strength',        'trainer_id' => $t1, 'sort_order' => 8],
                    ['time_slot' => '8:00 AM - 9:00 AM', 'day_of_week' => 'Tuesday',   'title' => 'Cardio',   'title_bn' => 'কার্ডিও',   'category' => 'Cardio',          'trainer_id' => $t4, 'sort_order' => 9],
                    ['time_slot' => '8:00 AM - 9:00 AM', 'day_of_week' => 'Wednesday', 'title' => 'Strength', 'title_bn' => 'স্ট্রেংথ',  'category' => 'Strength',        'trainer_id' => $t1, 'sort_order' => 10],
                    ['time_slot' => '8:00 AM - 9:00 AM', 'day_of_week' => 'Thursday',  'title' => 'Yoga',     'title_bn' => 'ইয়োগা',     'category' => 'Yoga',            'trainer_id' => $t3, 'sort_order' => 11],
                    ['time_slot' => '8:00 AM - 9:00 AM', 'day_of_week' => 'Friday',    'title' => 'Cardio',   'title_bn' => 'কার্ডিও',   'category' => 'Cardio',          'trainer_id' => $t4, 'sort_order' => 12],
                    ['time_slot' => '8:00 AM - 9:00 AM', 'day_of_week' => 'Saturday',  'title' => 'Strength', 'title_bn' => 'স্ট্রেংথ',  'category' => 'Strength',        'trainer_id' => $t1, 'sort_order' => 13],
                    ['time_slot' => '8:00 AM - 9:00 AM', 'day_of_week' => 'Sunday',    'title' => 'Rest',     'title_bn' => 'বিশ্রাম',   'category' => 'Rest',            'trainer_id' => null, 'sort_order' => 14],

                    // 5:00 PM - 6:00 PM
                    ['time_slot' => '5:00 PM - 6:00 PM', 'day_of_week' => 'Monday',    'title' => 'CrossFit', 'title_bn' => 'ক্রসফিট',   'category' => 'CrossFit',        'trainer_id' => $t2, 'sort_order' => 15],
                    ['time_slot' => '5:00 PM - 6:00 PM', 'day_of_week' => 'Tuesday',   'title' => 'Yoga',     'title_bn' => 'ইয়োগা',     'category' => 'Yoga',            'trainer_id' => $t3, 'sort_order' => 16],
                    ['time_slot' => '5:00 PM - 6:00 PM', 'day_of_week' => 'Wednesday', 'title' => 'CrossFit', 'title_bn' => 'ক্রসফিট',   'category' => 'CrossFit',        'trainer_id' => $t2, 'sort_order' => 17],
                    ['time_slot' => '5:00 PM - 6:00 PM', 'day_of_week' => 'Thursday',  'title' => 'CrossFit', 'title_bn' => 'ক্রসফিট',   'category' => 'CrossFit',        'trainer_id' => $t2, 'sort_order' => 18],
                    ['time_slot' => '5:00 PM - 6:00 PM', 'day_of_week' => 'Friday',    'title' => 'Strength', 'title_bn' => 'স্ট্রেংথ',  'category' => 'Strength',        'trainer_id' => $t1, 'sort_order' => 19],
                    ['time_slot' => '5:00 PM - 6:00 PM', 'day_of_week' => 'Saturday',  'title' => 'Yoga',     'title_bn' => 'ইয়োগা',     'category' => 'Yoga',            'trainer_id' => $t3, 'sort_order' => 20],
                    ['time_slot' => '5:00 PM - 6:00 PM', 'day_of_week' => 'Sunday',    'title' => 'Rest',     'title_bn' => 'বিশ্রাম',   'category' => 'Rest',            'trainer_id' => null, 'sort_order' => 21],

                    // 7:00 PM - 8:00 PM
                    ['time_slot' => '7:00 PM - 8:00 PM', 'day_of_week' => 'Monday',    'title' => 'Cardio',   'title_bn' => 'কার্ডিও',   'category' => 'Cardio',          'trainer_id' => $t4, 'sort_order' => 22],
                    ['time_slot' => '7:00 PM - 8:00 PM', 'day_of_week' => 'Tuesday',   'title' => 'Strength', 'title_bn' => 'স্ট্রেংথ',  'category' => 'Strength',        'trainer_id' => $t1, 'sort_order' => 23],
                    ['time_slot' => '7:00 PM - 8:00 PM', 'day_of_week' => 'Wednesday', 'title' => 'Yoga',     'title_bn' => 'ইয়োগা',     'category' => 'Yoga',            'trainer_id' => $t3, 'sort_order' => 24],
                    ['time_slot' => '7:00 PM - 8:00 PM', 'day_of_week' => 'Thursday',  'title' => 'Strength', 'title_bn' => 'স্ট্রেংথ',  'category' => 'Strength',        'trainer_id' => $t1, 'sort_order' => 25],
                    ['time_slot' => '7:00 PM - 8:00 PM', 'day_of_week' => 'Friday',    'title' => 'CrossFit', 'title_bn' => 'ক্রসফিট',   'category' => 'CrossFit',        'trainer_id' => $t2, 'sort_order' => 26],
                    ['time_slot' => '7:00 PM - 8:00 PM', 'day_of_week' => 'Saturday',  'title' => 'Cardio',   'title_bn' => 'কার্ডিও',   'category' => 'Cardio',          'trainer_id' => $t4, 'sort_order' => 27],
                    ['time_slot' => '7:00 PM - 8:00 PM', 'day_of_week' => 'Sunday',    'title' => 'Rest',     'title_bn' => 'বিশ্রাম',   'category' => 'Rest',            'trainer_id' => null, 'sort_order' => 28],
                ];

                foreach ($scheduleRows as $row) {
                    GymClass::create(array_merge($row, [
                        'category_bn'      => $row['title_bn'],
                        'duration_minutes' => 60,
                        'room_zone'        => $row['category'] === 'Yoga' ? 'Zen Mobility Studio' : ($row['category'] === 'CrossFit' ? 'CrossFit Arena' : 'Main Gym Floor'),
                        'max_participants' => 25,
                        'booked_count'     => rand(6, 18),
                        'is_active'        => true,
                    ]));
                }
            }

            // 5. Seed 6 Gym Equipment Items (Exact match to Reference Image)
            if (GymEquipment::count() === 0) {
                $equipments = [
                    [
                        'name'                  => 'Treadmill',
                        'name_bn'               => 'ট্রেডমিল (কার্ডিও প্রো)',
                        'category'              => 'Cardio',
                        'quantity'              => 12,
                        'brand'                 => 'Technogym Artis Run',
                        'image'                 => '/module-media/GymFitness/theme/equip-treadmill.jpg',
                        'condition_status'      => 'Operational',
                        'last_maintenance_date' => Carbon::now()->subDays(20),
                        'next_maintenance_date' => Carbon::now()->addDays(40),
                        'notes'                 => 'Incline & heart-rate sensors calibrated.',
                        'sort_order'            => 1,
                    ],
                    [
                        'name'                  => 'Dumbbells',
                        'name_bn'               => 'ডাম্বেল সেট (2.5kg - 50kg)',
                        'category'              => 'Free Weights',
                        'quantity'              => 40,
                        'brand'                 => 'Rogue Urethane Pro',
                        'image'                 => '/module-media/GymFitness/theme/equip-dumbbells.jpg',
                        'condition_status'      => 'Operational',
                        'last_maintenance_date' => Carbon::now()->subDays(15),
                        'next_maintenance_date' => Carbon::now()->addDays(75),
                        'notes'                 => 'Full rack 2.5kg to 50kg pairs.',
                        'sort_order'            => 2,
                    ],
                    [
                        'name'                  => 'Smith Machine',
                        'name_bn'               => 'স্মিথ মেশিন',
                        'category'              => 'Strength',
                        'quantity'              => 3,
                        'brand'                 => 'Hammer Strength Linear',
                        'image'                 => '/module-media/GymFitness/theme/equip-smith.jpg',
                        'condition_status'      => 'Operational',
                        'last_maintenance_date' => Carbon::now()->subDays(10),
                        'next_maintenance_date' => Carbon::now()->addDays(50),
                        'notes'                 => 'Counter-balanced linear bearings lubricated.',
                        'sort_order'            => 3,
                    ],
                    [
                        'name'                  => 'Bench Press',
                        'name_bn'               => 'বেঞ্চ প্রেস র‍্যাক',
                        'category'              => 'Strength',
                        'quantity'              => 6,
                        'brand'                 => 'Life Fitness Olympic',
                        'image'                 => '/module-media/GymFitness/theme/equip-bench.jpg',
                        'condition_status'      => 'Operational',
                        'last_maintenance_date' => Carbon::now()->subDays(25),
                        'next_maintenance_date' => Carbon::now()->addDays(65),
                        'notes'                 => 'Flat, incline, and decline Olympic benches.',
                        'sort_order'            => 4,
                    ],
                    [
                        'name'                  => 'Rowing Machine',
                        'name_bn'               => 'রোয়িং মেশিন',
                        'category'              => 'Cardio',
                        'quantity'              => 5,
                        'brand'                 => 'Concept2 RowErg',
                        'image'                 => '/module-media/GymFitness/theme/prog-cardio.jpg',
                        'condition_status'      => 'Operational',
                        'last_maintenance_date' => Carbon::now()->subDays(12),
                        'next_maintenance_date' => Carbon::now()->addDays(48),
                        'notes'                 => 'PM5 performance monitors updated.',
                        'sort_order'            => 5,
                    ],
                    [
                        'name'                  => 'Exercise Bike',
                        'name_bn'               => 'এক্সারসাইজ স্পিন বাইক',
                        'category'              => 'Cardio',
                        'quantity'              => 10,
                        'brand'                 => 'Schwinn AC Performance',
                        'image'                 => '/module-media/GymFitness/theme/gym-interior.jpg',
                        'condition_status'      => 'Operational',
                        'last_maintenance_date' => Carbon::now()->subDays(18),
                        'next_maintenance_date' => Carbon::now()->addDays(42),
                        'notes'                 => 'Magnetic resistance spin bikes.',
                        'sort_order'            => 6,
                    ],
                ];

                foreach ($equipments as $eq) {
                    GymEquipment::create(array_merge($eq, ['is_active' => true]));
                }
            }

            // 6. Seed Workout & Diet Plans
            if (GymFitnessPlan::count() === 0) {
                $plans = [
                    [
                        'title'               => 'Hypertrophy 5-Day Split Workout',
                        'title_bn'            => 'হাইপারট্রফি ৫-দিনের মাসল বিল্ডিং ওয়ার্কআউট',
                        'plan_type'           => 'Workout',
                        'goal'                => 'Muscle Gain',
                        'level'               => 'Intermediate',
                        'duration_weeks'      => 8,
                        'calories_target'     => '600 kcal burn / session',
                        'schedule_details'    => "Day 1: Chest & Triceps (Bench Press 4x10, Incline Dumbbell 4x12, Cable Fly 3x15)\nDay 2: Back & Biceps (Deadlift 4x8, Lat Pulldown 4x12, Barbell Curl 3x12)\nDay 3: Active Recovery & Mobility\nDay 4: Legs & Core (Squats 4x10, Leg Press 4x12, RDL 3x10)\nDay 5: Shoulders & Arms (Overhead Press 4x10, Lateral Raises 4x15)",
                        'schedule_details_bn' => "দিন ১: চেস্ট ও ট্রাইসেপস (বেঞ্চ প্রেস ৪x১০, ইনক্লাইন ডাম্বেল ৪x১২)\nদিন ২: ব্যাক ও বাইসেপস (ডেডলিফট ৪x৮, ল্যাট পুলডাউন ৪x১২)\nদিন ৩: অ্যাক্টিভ রিকভারি ও স্ট্রেচিং\nদিন ৪: লেগস ও কোর (স্কোয়াট ৪x১০, লেগ প্রেস ৪x১২)\nদিন ৫: শোল্ডার ও আর্মস (ওভারহেড প্রেস ৪x১০)",
                        'is_active'           => true,
                    ],
                    [
                        'title'               => 'HIIT Shred & Fat Loss Protocol',
                        'title_bn'            => 'দ্রুত ফ্যাট লস ও HIIT ওয়ার্কআউট রুটিন',
                        'plan_type'           => 'Workout',
                        'goal'                => 'Fat Loss',
                        'level'               => 'Beginner',
                        'duration_weeks'      => 6,
                        'calories_target'     => '700 kcal burn / session',
                        'schedule_details'    => "Mon/Wed/Fri: 20m Full-Body Circuit + 25m HIIT Treadmill Sprints\nTue/Thu: CrossFit Metabolic Conditioning & Core\nSat: 60m Yoga & Mobility Flow",
                        'schedule_details_bn' => "সোম/বুধ/শুক্র: ২০ মিনিট ফুল-বডি সার্কিট + ২৫ মিনিট ট্রেডমিল HIIT\nমঙ্গল/বৃহস্পতি: ক্রসফিট কন্ডিশনিং ও কোর\nশনিবার: ৬০ মিনিট ইয়োগা ও ফ্লেক্সিবিলিটি",
                        'is_active'           => true,
                    ],
                    [
                        'title'               => 'High-Protein Lean Muscle Diet Plan',
                        'title_bn'            => 'হাই-প্রোটিন লিন মাসল ডায়েট চার্ট',
                        'plan_type'           => 'Diet',
                        'goal'                => 'Muscle Gain',
                        'level'               => 'Intermediate',
                        'duration_weeks'      => 8,
                        'calories_target'     => '2,500 kcal / day (180g Protein)',
                        'schedule_details'    => "Breakfast (8:00 AM): 4 Egg Whites + 2 Whole Eggs, 80g Oats, 1 Banana\nLunch (1:30 PM): 200g Grilled Chicken Breast, 1.5 Cup Brown Rice, Mixed Green Salad\nPre-Workout (5:00 PM): Black Coffee + 1 Apple + 5 Almonds\nPost-Workout (7:30 PM): 1 Scoop Whey Protein + 5g Creatine\nDinner (9:00 PM): 200g Grilled Fish / Chicken, Steamed Broccoli & Sweet Potato",
                        'schedule_details_bn' => "সকালের নাস্তা: ৪টি ডিমের সাদা অংশ + ২টি পূর্ণ ডিম, ওটস এবং ১টি কলা\nদুপুরের খাবার: ২০০ গ্রাম গ্রিলড চিকেন/মাছ, লাল চালের ভাত ও সবজি সালাদ\nপ্রি-ওয়ার্কআউট: ব্ল্যাক কফি + ১টি আপেল ও কাঠবাদাম\nরাতের খাবার: ২০০ গ্রাম মাছ বা চিকেন ব্রেস্ট, ব্রকলি ও মিষ্টি আলু",
                        'is_active'           => true,
                    ],
                    [
                        'title'               => 'Calorie-Deficit Fat Shred Nutrition Plan',
                        'title_bn'            => 'ক্যালোরি-ডেফিসিট ওজন কমানোর ডায়েট প্ল্যান',
                        'plan_type'           => 'Diet',
                        'goal'                => 'Fat Loss',
                        'level'               => 'Beginner',
                        'duration_weeks'      => 6,
                        'calories_target'     => '1,750 kcal / day (145g Protein)',
                        'schedule_details'    => "Morning: Warm Lemon Water + 3 Boiled Eggs + Cucumber\nLunch: 150g Grilled Chicken/Fish, 1 Cup Rice/2 Roti, Plenty of Leafy Greens\nEvening Snack: Green Tea + Roasted Chickpeas / 10 Almonds\nDinner: Chicken Vegetable Soup or Grilled Fish with Salad (No Late Carbs)",
                        'schedule_details_bn' => "সকাল: কুসুম গরম লেবু পানি + ৩টি সেদ্ধ ডিম ও শসা\nদুপুর: ১৫০ গ্রাম চিকেন/মাছ, ১ কাপ ভাত বা ২টি লাল আটার রুটি, প্রচুর শাকসবজি\nবিকাল: গ্রিন টি + ১০টি কাঠবাদাম\nরাত: চিকেন ভেজিটেবল স্যুপ অথবা গ্রিলড ফিশ ও সালাদ",
                        'is_active'           => true,
                    ],
                ];

                foreach ($plans as $pl) {
                    GymFitnessPlan::create($pl);
                }
            }

            // 7. Seed 3 Testimonials (Exact match to Reference Image)
            if (GymTestimonial::count() === 0) {
                $testimonials = [
                    [
                        'member_name'           => 'Tanvir Ahmed',
                        'member_name_bn'        => 'তানভীর আহমেদ',
                        'transformation_tag'    => 'Lost 15kg in 5 Months',
                        'transformation_tag_bn' => '৫ মাসে ১৫ কেজি ওজন কমিয়েছেন',
                        'review_text'           => 'FitPulse Gym has completely changed my life. The trainers are amazing and the equipment is top-notch!',
                        'review_text_bn'        => 'ফিটপালস জিম আমার জীবন পুরোপুরি বদলে দিয়েছে। এখানকার ট্রেইনাররা অসাধারণ এবং যন্ত্রপাতি বিশ্বমানের!',
                        'rating'                => 5,
                        'avatar'                => '/module-media/GymFitness/theme/trainer-1.jpg',
                        'sort_order'            => 1,
                    ],
                    [
                        'member_name'           => 'Nusrat Jahan',
                        'member_name_bn'        => 'নুসরাত জাহান',
                        'transformation_tag'    => 'Core Strength & Mobility',
                        'transformation_tag_bn' => 'কোর স্ট্রেংথ ও ফিটনেস অর্জন',
                        'review_text'           => 'Best gym in town! Highly recommended for everyone looking for a clean, safe and motivating environment.',
                        'review_text_bn'        => 'শহরের সেরা জিম! পরিচ্ছন্ন, নিরাপদ এবং অনুপ্রেরণামূলক পরিবেশের জন্য সবাইকে আমি ফিটপালস রেকমেন্ড করি।',
                        'rating'                => 5,
                        'avatar'                => '/module-media/GymFitness/theme/trainer-3.jpg',
                        'sort_order'            => 2,
                    ],
                    [
                        'member_name'           => 'Rakibul Islam',
                        'member_name_bn'        => 'রাকিবুল ইসলাম',
                        'transformation_tag'    => 'Gained 8kg Lean Muscle',
                        'transformation_tag_bn' => '৮ কেজি লিন মাসল বৃদ্ধি',
                        'review_text'           => 'Great facilities and friendly staff. I feel healthier and stronger every single week.',
                        'review_text_bn'        => 'চমৎকার সুযোগ-সুবিধা এবং আন্তরিক স্টাফ। প্রতিটি সপ্তাহে আমি নিজেকে আরও সুস্থ ও শক্তিশালী অনুভব করছি।',
                        'rating'                => 5,
                        'avatar'                => '/module-media/GymFitness/theme/trainer-4.jpg',
                        'sort_order'            => 3,
                    ],
                ];

                foreach ($testimonials as $t) {
                    GymTestimonial::create(array_merge($t, ['is_active' => true]));
                }
            }

            // 8. Seed 4 FAQs (Exact match to Reference Image)
            if (GymFaq::count() === 0) {
                $faqs = [
                    [
                        'question'    => 'What are your gym hours?',
                        'question_bn' => 'আপনাদের জিমের সময়সূচী কী?',
                        'answer'      => 'We are open Monday through Sunday from 6:00 AM to 11:00 PM (Elite members enjoy 24/7 access).',
                        'answer_bn'   => 'আমাদের জিম প্রতিদিন সকাল ৬টা থেকে রাত ১১টা পর্যন্ত খোলা থাকে (এলিট মেম্বারদের জন্য ২৪/৭ অ্যাক্সেস)।',
                        'sort_order'  => 1,
                    ],
                    [
                        'question'    => 'Do you provide personal trainers?',
                        'question_bn' => 'আপনারা কি পার্সোনাল ট্রেইনার সুবিধা দেন?',
                        'answer'      => 'Yes, certified trainers are available for 1-on-1 coaching.',
                        'answer_bn'   => 'হ্যাঁ, ১-অন-১ কোচিংয়ের জন্য আমাদের সার্টিফাইড পার্সোনাল ট্রেইনার রয়েছেন।',
                        'sort_order'  => 2,
                    ],
                    [
                        'question'    => 'Can I freeze my membership?',
                        'question_bn' => 'আমি কি মেম্বারশিপ ফ্রিজ করতে পারব?',
                        'answer'      => 'Yes, you can freeze your membership anytime from your Member Portal or by contacting our front desk.',
                        'answer_bn'   => 'হ্যাঁ, আপনি যেকোনো সময় আপনার মেম্বার পোর্টাল থেকে অথবা আমাদের ফ্রন্ট ডেস্কে যোগাযোগ করে মেম্বারশিপ ফ্রিজ করতে পারবেন।',
                        'sort_order'  => 3,
                    ],
                    [
                        'question'    => 'Is there a free trial available?',
                        'question_bn' => 'ফ্রি ট্রায়াল সুবিধা কি পাওয়া যাবে?',
                        'answer'      => 'Yes! Click Start Free Trial on the homepage to claim your complimentary trial session with our coaches.',
                        'answer_bn'   => 'হ্যাঁ! আমাদের কোচদের সাথে ফ্রি ট্রায়াল সেশন বুক করতে হোমপেজের ফ্রি ট্রায়াল বাটনে ক্লিক করুন।',
                        'sort_order'  => 4,
                    ],
                ];

                foreach ($faqs as $faq) {
                    GymFaq::create(array_merge($faq, ['is_active' => true]));
                }
            }

            // 9. Seed Sample Gym Members, Subscriptions, Payments, Attendance & Progress Logs
            if (GymMember::count() === 0) {
                $premiumPkg = GymPackage::where('slug', 'premium-plan')->first() ?? GymPackage::first();
                $elitePkg   = GymPackage::where('slug', 'elite-plan')->first() ?? $premiumPkg;
                $basicPkg   = GymPackage::where('slug', 'basic-plan')->first() ?? $premiumPkg;
                $trainer1   = GymTrainer::first();
                $trainer2   = GymTrainer::skip(1)->first() ?? $trainer1;
                $workout1   = GymFitnessPlan::where('plan_type', 'Workout')->first();
                $diet1      = GymFitnessPlan::where('plan_type', 'Diet')->first();

                $sampleMembers = [
                    [
                        'member_code'             => 'FP-2026-0001',
                        'qr_token'                => 'QR-FP-2026-0001',
                        'name'                    => 'Tanvir Ahmed',
                        'email'                   => 'tanvir.member@fitpulse.com',
                        'phone'                   => '01711-889900',
                        'date_of_birth'           => '1996-05-14',
                        'gender'                  => 'Male',
                        'blood_group'             => 'O+',
                        'address'                 => 'House 24, Road 11, Banani, Dhaka',
                        'emergency_contact_name'  => 'Rashed Ahmed',
                        'emergency_contact_phone' => '01711-001122',
                        'package_id'              => $premiumPkg?->id,
                        'trainer_id'              => $trainer1?->id,
                        'workout_plan_id'         => $workout1?->id,
                        'diet_plan_id'            => $diet1?->id,
                        'billing_cycle'           => 'quarterly',
                        'join_date'               => Carbon::now()->subMonths(2)->toDateString(),
                        'expiry_date'             => Carbon::now()->addMonth()->toDateString(),
                        'membership_status'       => 'Active',
                        'payment_status'          => 'Paid',
                        'due_amount'              => 0.00,
                        'photo'                   => '/module-media/GymFitness/theme/trainer-1.jpg',
                    ],
                    [
                        'member_code'             => 'FP-2026-0002',
                        'qr_token'                => 'QR-FP-2026-0002',
                        'name'                    => 'Sadia Islam',
                        'email'                   => 'sadia.member@fitpulse.com',
                        'phone'                   => '01819-554433',
                        'date_of_birth'           => '1999-08-22',
                        'gender'                  => 'Female',
                        'blood_group'             => 'A+',
                        'address'                 => 'Sector 7, Uttara, Dhaka',
                        'emergency_contact_name'  => 'Kamrul Islam',
                        'emergency_contact_phone' => '01819-112233',
                        'package_id'              => $elitePkg?->id,
                        'trainer_id'              => $trainer2?->id,
                        'workout_plan_id'         => $workout1?->id,
                        'diet_plan_id'            => $diet1?->id,
                        'billing_cycle'           => 'yearly',
                        'join_date'               => Carbon::now()->subMonths(4)->toDateString(),
                        'expiry_date'             => Carbon::now()->addMonths(8)->toDateString(),
                        'membership_status'       => 'Active',
                        'payment_status'          => 'Paid',
                        'due_amount'              => 0.00,
                        'photo'                   => '/module-media/GymFitness/theme/trainer-3.jpg',
                    ],
                    [
                        'member_code'             => 'FP-2026-0003',
                        'qr_token'                => 'QR-FP-2026-0003',
                        'name'                    => 'Mahmudul Hasan',
                        'email'                   => 'mahmud@fitpulse.com',
                        'phone'                   => '01912-776655',
                        'date_of_birth'           => '1994-11-03',
                        'gender'                  => 'Male',
                        'blood_group'             => 'B+',
                        'address'                 => 'Dhanmondi 27, Dhaka',
                        'emergency_contact_name'  => 'Farida Yasmin',
                        'emergency_contact_phone' => '01912-334411',
                        'package_id'              => $basicPkg?->id,
                        'trainer_id'              => $trainer1?->id,
                        'workout_plan_id'         => $workout1?->id,
                        'diet_plan_id'            => $diet1?->id,
                        'billing_cycle'           => 'monthly',
                        'join_date'               => Carbon::now()->subDays(35)->toDateString(),
                        'expiry_date'             => Carbon::now()->subDays(5)->toDateString(),
                        'membership_status'       => 'Expired',
                        'payment_status'          => 'Due',
                        'due_amount'              => 19.00,
                        'photo'                   => '/module-media/GymFitness/theme/trainer-4.jpg',
                    ],
                ];

                foreach ($sampleMembers as $idx => $mData) {
                    $member = GymMember::create(array_merge($mData, [
                        'password' => Hash::make('123456'),
                    ]));

                    $amt = $idx === 0 ? 99.00 : ($idx === 1 ? 449.00 : 19.00);
                    GymSubscription::create([
                        'member_id'        => $member->id,
                        'package_id'       => $member->package_id,
                        'plan_name'        => $member->package?->name ?? 'Premium Plan',
                        'billing_cycle'    => $member->billing_cycle,
                        'action_type'      => 'Registration',
                        'amount'           => $amt,
                        'registration_fee' => 0,
                        'start_date'       => $member->join_date,
                        'end_date'         => $member->expiry_date,
                        'status'           => $member->membership_status,
                    ]);

                    $payment = GymPayment::create([
                        'invoice_no'            => 'INV-GYM-' . (1001 + $idx),
                        'member_id'             => $member->id,
                        'package_id'            => $member->package_id,
                        'payment_type'          => 'Membership Fee',
                        'amount'                => $amt,
                        'discount_amount'       => 0,
                        'paid_amount'           => $member->payment_status === 'Paid' ? $amt : 0,
                        'due_amount'            => $member->due_amount,
                        'payment_method'        => $idx === 0 ? 'bKash / Nagad' : ($idx === 1 ? 'Card / PGW' : 'Cash at Desk'),
                        'transaction_reference' => 'TXN-FP-' . (9001 + $idx),
                        'payment_status'        => $member->payment_status === 'Paid' ? 'Paid' : 'Pending',
                        'paid_at'               => $member->payment_status === 'Paid' ? now()->subDays($idx * 2) : null,
                        'notes'                 => 'Initial membership registration invoice.',
                    ]);

                    $this->syncWithCoreTransactions($payment, $member->phone);

                    // Seed Today's Attendance for active members
                    if ($member->membership_status === 'Active') {
                        GymAttendance::create([
                            'member_id'       => $member->id,
                            'attendance_date' => Carbon::today()->toDateString(),
                            'check_in_time'   => $idx === 0 ? '07:15 AM' : '08:05 AM',
                            'check_out_time'  => $idx === 0 ? '08:40 AM' : null,
                            'check_in_method' => 'QR Code',
                            'status'          => $idx === 0 ? 'Completed' : 'Present',
                            'notes'           => 'Checked in at Main Turnstile',
                        ]);
                    }

                    // Seed Progress Logs for first member
                    if ($idx === 0) {
                        GymProgressLog::create([
                            'member_id'     => $member->id,
                            'recorded_date' => Carbon::now()->subMonths(2)->toDateString(),
                            'weight_kg'     => 86.5,
                            'height_cm'     => 175,
                            'bmi'           => 28.2,
                            'body_fat_pct'  => 24.5,
                            'chest_cm'      => 102,
                            'waist_cm'      => 94,
                            'arms_cm'       => 36,
                            'notes'         => 'Baseline assessment on joining day.',
                        ]);
                        GymProgressLog::create([
                            'member_id'     => $member->id,
                            'recorded_date' => Carbon::now()->subMonth()->toDateString(),
                            'weight_kg'     => 81.0,
                            'height_cm'     => 175,
                            'bmi'           => 26.4,
                            'body_fat_pct'  => 20.8,
                            'chest_cm'      => 104,
                            'waist_cm'      => 88,
                            'arms_cm'       => 37.5,
                            'notes'         => 'Month 1 check-in: -5.5kg fat loss & strength up.',
                        ]);
                        GymProgressLog::create([
                            'member_id'     => $member->id,
                            'recorded_date' => Carbon::today()->toDateString(),
                            'weight_kg'     => 76.2,
                            'height_cm'     => 175,
                            'bmi'           => 24.9,
                            'body_fat_pct'  => 17.2,
                            'chest_cm'      => 106,
                            'waist_cm'      => 82,
                            'arms_cm'       => 39.0,
                            'notes'         => 'Month 2 check-in: Target BMI reached!',
                        ]);
                    }
                }

                // Seed a sample class booking
                $firstClass  = GymClass::where('category', '!=', 'Rest')->first();
                $firstMember = GymMember::first();
                if ($firstClass && $firstMember) {
                    GymClassBooking::create([
                        'class_id'     => $firstClass->id,
                        'member_id'    => $firstMember->id,
                        'booking_date' => Carbon::today()->toDateString(),
                        'status'       => 'Booked',
                        'notes'        => 'Morning session reserved via Member Portal',
                    ]);
                }
            }

            // 10. Seed Standard Module CMS Tables (gym_themes, gym_menus, gym_pages, gym_videos, gym_media, gym_sliders, gym_users)
            if (ModuleTheme::count() === 0) {
                ModuleTheme::create([
                    'name'            => 'FitPulse Dark Neon Lime Pro',
                    'slug'            => 'fitpulse-dark-neon-lime',
                    'primary_color'   => '#A3E635',
                    'secondary_color' => '#0A0E0A',
                    'font_family'     => 'Plus Jakarta Sans & Teko & Hind Siliguri',
                    'layout_style'    => 'FitPulse High-Contrast Fitness Grid',
                    'thumbnail'       => '/module-media/GymFitness/theme/hero-athlete.jpg',
                    'is_active'       => true,
                ]);
                ModuleTheme::create([
                    'name'            => 'FitPulse Emerald Energy Edition',
                    'slug'            => 'fitpulse-emerald-energy',
                    'primary_color'   => '#22C55E',
                    'secondary_color' => '#09140E',
                    'font_family'     => 'Plus Jakarta Sans & Hind Siliguri',
                    'layout_style'    => 'Athletic Performance Dark',
                    'thumbnail'       => '/module-media/GymFitness/theme/gym-interior.jpg',
                    'is_active'       => false,
                ]);
            }

            if (ModuleMenu::count() === 0) {
                $menus = [
                    ['title' => 'Home',          'title_bn' => 'হোম',              'url' => '#hero',         'location' => 'header', 'sort_order' => 1],
                    ['title' => 'About',         'title_bn' => 'আমাদের সম্পর্কে',   'url' => '#why-choose',   'location' => 'header', 'sort_order' => 2],
                    ['title' => 'Programs',      'title_bn' => 'প্রোগ্রামসমূহ',     'url' => '#programs',     'location' => 'header', 'sort_order' => 3],
                    ['title' => 'Pricing',       'title_bn' => 'প্যাকেজ ও মূল্য',    'url' => '#pricing',      'location' => 'header', 'sort_order' => 4],
                    ['title' => 'Schedule',      'title_bn' => 'ক্লাস রুটিন',       'url' => '#schedule',     'location' => 'header', 'sort_order' => 5],
                    ['title' => 'Trainers',      'title_bn' => 'ট্রেইনারবৃন্দ',     'url' => '#trainers',     'location' => 'header', 'sort_order' => 6],
                    ['title' => 'Equipment',     'title_bn' => 'ইকুইপমেন্ট',        'url' => '#equipment',    'location' => 'header', 'sort_order' => 7],
                    ['title' => 'FAQ',           'title_bn' => 'জিজ্ঞাসা',          'url' => '#faq',          'location' => 'header', 'sort_order' => 8],
                    ['title' => 'Member Portal', 'title_bn' => 'মেম্বার পোর্টাল',   'url' => '/gym-fitness/portal', 'location' => 'footer', 'sort_order' => 1],
                ];
                foreach ($menus as $m) {
                    ModuleMenu::create(array_merge($m, ['target' => '_self', 'is_active' => true]));
                }
            }

            if (ModulePage::count() === 0) {
                ModulePage::create([
                    'title'   => 'Membership Rules & Gym Etiquette',
                    'slug'    => 'membership-rules',
                    'content' => '<p>Welcome to FitPulse Gym & Fitness. Please bring your digital QR pass for check-in and use a sweat towel on all benches.</p>',
                    'status'  => 'Published',
                    'views'   => 640,
                ]);
                ModulePage::create([
                    'title'   => 'Personal Training & Body Transformation Guide',
                    'slug'    => 'personal-training-guide',
                    'content' => '<p>Learn how our certified trainers track your macros, BMI, and progressive overload every week.</p>',
                    'status'  => 'Published',
                    'views'   => 520,
                ]);
            }

            if (ModuleVideo::count() < 3) {
                $existingVideos = ModuleVideo::count();
                if ($existingVideos === 0) {
                    ModuleVideo::create([
                        'title'       => 'Inside IronPulse Gym — Full Facility Tour & Strength Arena',
                        'title_bn'    => 'আয়রনপালস জিমের সম্পূর্ণ ট্যুর ও স্ট্রেংথ এরিনা',
                        'description' => 'Take a tour of our Hammer Strength weight room, CrossFit turf arena, and cardio zone.',
                        'category'    => 'Facility Tour',
                        'video_url'   => 'https://www.youtube.com/embed/ScMzIvxBSi4',
                        'thumbnail'   => '/module-media/GymFitness/theme/gym-interior.jpg',
                        'duration'    => '03:45',
                        'sort_order'  => 1,
                        'is_active'   => true,
                    ]);
                }
                if ($existingVideos < 2) {
                    ModuleVideo::create([
                        'title'       => 'High-Intensity Fat Burn & HIIT Conditioning Session',
                        'title_bn'    => 'হাই-ইনটেনসিটি ফ্যাট বার্ন ও HIIT কন্ডিশনিং সেশন',
                        'description' => 'Watch our certified coaches lead an intense 45-minute metabolic conditioning circuit.',
                        'category'    => 'HIIT & Cardio',
                        'video_url'   => 'https://www.youtube.com/embed/ScMzIvxBSi4',
                        'thumbnail'   => '/module-media/GymFitness/theme/prog-cardio.jpg',
                        'duration'    => '04:20',
                        'sort_order'  => 2,
                        'is_active'   => true,
                    ]);
                    ModuleVideo::create([
                        'title'       => 'Progressive Overload & Compound Strength Masterclass',
                        'title_bn'    => 'প্রগ্রেসিভ ওভারলোড ও স্ট্রেংথ ট্রেনিং মাস্টারক্লাস',
                        'description' => 'Learn proper form for deadlifts, squats, and bench press with Master Coach Marvin McKinney.',
                        'category'    => 'Strength Training',
                        'video_url'   => 'https://www.youtube.com/embed/ScMzIvxBSi4',
                        'thumbnail'   => '/module-media/GymFitness/theme/prog-weight.jpg',
                        'duration'    => '05:15',
                        'sort_order'  => 3,
                        'is_active'   => true,
                    ]);
                }
            }

            if (ModuleMedia::count() < 6) {
                $gallerySamples = [
                    ['title' => 'IronPulse Main Strength Arena',     'category' => 'Strength Zone',  'file_path' => '/module-media/GymFitness/theme/gym-interior.jpg',   'alt_text' => 'Strength Arena'],
                    ['title' => 'Olympic Free Weights & Dumbbells',  'category' => 'Free Weights',   'file_path' => '/module-media/GymFitness/theme/equip-dumbbells.jpg','alt_text' => 'Free Weights'],
                    ['title' => 'Cardio & HIIT Performance Zone',    'category' => 'Cardio Zone',    'file_path' => '/module-media/GymFitness/theme/prog-cardio.jpg',    'alt_text' => 'Cardio Zone'],
                    ['title' => '1-on-1 Personal Coaching Studio',   'category' => 'Personal Coaching','file_path' => '/module-media/GymFitness/theme/prog-personal.jpg', 'alt_text' => 'Personal Training'],
                    ['title' => 'Group CrossFit & Functional Turf',  'category' => 'CrossFit Turf',  'file_path' => '/module-media/GymFitness/theme/prog-crossfit.jpg',  'alt_text' => 'CrossFit Turf'],
                    ['title' => 'Zen Yoga & Mobility Studio',        'category' => 'Yoga & Recovery','file_path' => '/module-media/GymFitness/theme/prog-yoga.jpg',      'alt_text' => 'Yoga Studio'],
                ];
                foreach ($gallerySamples as $gItem) {
                    if (!ModuleMedia::where('title', $gItem['title'])->exists()) {
                        ModuleMedia::create(array_merge($gItem, [
                            'file_type' => 'image/jpeg',
                            'file_size' => '120 KB',
                        ]));
                    }
                }
            }

            if (ModuleSlider::count() === 0) {
                ModuleSlider::create([
                    'title'       => 'NO EXCUSES. ONLY RESULTS.',
                    'title_bn'    => 'কোনো অজুহাত নয়। শুধুমাত্র ফলাফল।',
                    'subtitle'    => 'Transform your body, strengthen your mind and live a healthier life with FitPulse Gym.',
                    'button_text' => 'Get Membership',
                    'button_url'  => '#pricing',
                    'image'       => '/module-media/GymFitness/theme/hero-athlete.jpg',
                    'sort_order'  => 1,
                    'is_active'   => true,
                ]);
            }

            if (ModuleUser::count() === 0) {
                ModuleUser::create([
                    'name'      => 'Farhan Rahman (Head Coach & Gym Manager)',
                    'email'     => 'farhan@fitpulse.com',
                    'phone'     => '+880 1711-223344',
                    'role_name' => 'Administrator',
                    'status'    => 'Active',
                ]);
            }
        } catch (\Throwable $e) {
            // Fail-safe during early migrations
        }
    }
}
