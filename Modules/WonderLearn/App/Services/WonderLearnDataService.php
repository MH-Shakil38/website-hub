<?php

namespace Modules\WonderLearn\App\Services;

use Illuminate\Support\Facades\Schema;
use Modules\WonderLearn\App\Models\ModuleMedia;
use Modules\WonderLearn\App\Models\ModuleMenu;
use Modules\WonderLearn\App\Models\ModulePage;
use Modules\WonderLearn\App\Models\ModuleSetting;
use Modules\WonderLearn\App\Models\ModuleSlider;
use Modules\WonderLearn\App\Models\ModuleTheme;
use Modules\WonderLearn\App\Models\ModuleUser;
use Modules\WonderLearn\App\Models\ModuleVideo;
use Modules\WonderLearn\App\Models\WlAdventure;
use Modules\WonderLearn\App\Models\WlEnrollment;
use Modules\WonderLearn\App\Models\WlGame;
use Modules\WonderLearn\App\Models\WlStory;
use Modules\WonderLearn\App\Models\WlSubscriber;
use Modules\WonderLearn\App\Models\WlTestimonial;
use Modules\WonderLearn\App\Models\WlTopic;

class WonderLearnDataService
{
    public function getDefaultSettings(): array
    {
        return [
            'brand_name'          => 'WonderLearn',
            'brand_tagline'       => 'Learn • Discover • Grow',
            'logo_icon'           => '/module-media/WonderLearn/theme/logo-book-icon.png',
            'login_text'          => 'Log In',
            'join_text'           => 'Join Now',
            'hero_title_line1'    => 'Let Curiosity',
            'hero_title_line2'    => 'Lead the Way',
            'hero_subtitle'       => 'A magical world where learning feels like an adventure!',
            'hero_btn_text'       => 'Start Exploring',
            'hero_bg_image'       => '/module-media/WonderLearn/theme/hero-magic-world.jpg',
            'trust_badge_1'       => 'Safe & Ad-Free',
            'trust_badge_2'       => 'Expert Curated',
            'trust_badge_3'       => 'Learn Through Play',
            'trust_badge_4'       => 'Age 4+ to 12',
            'topics_heading'      => 'Explore by Topic',
            'mascot_bunny'        => '/module-media/WonderLearn/theme/mascot-bunny.png',
            'mascot_signpost'     => '/module-media/WonderLearn/theme/mascot-signpost.png',
            'stories_heading'     => 'Magical Stories',
            'stories_btn_text'    => 'View All Stories',
            'games_heading'       => 'Play & Learn',
            'games_btn_text'      => 'View All Games',
            'adventures_heading'  => 'Featured Adventures',
            'parents_heading'     => 'Loved by Parents',
            'deco_butterfly'      => '/module-media/WonderLearn/theme/deco-butterfly.png',
            'app_heading'         => 'Learning on the Go!',
            'app_subtext'         => 'Download the WonderLearn app and access fun educational content anytime!',
            'mascot_monster'      => '/module-media/WonderLearn/theme/mascot-monster-tablet.png',
            'mascot_fox'          => '/module-media/WonderLearn/theme/mascot-fox-phone.png',
            'footer_tagline'      => 'Making learning magical for every child.',
            'newsletter_title'    => 'Stay Connected',
            'newsletter_subtitle' => 'Get the latest updates and more.',
            'contact_email'       => 'hello@wonderlearn.com',
            'contact_phone'       => '+1 (800) 555-KIDS',
        ];
    }

    public function getSettingsData(): array
    {
        $defaults = $this->getDefaultSettings();

        if (!Schema::connection('tenant')->hasTable('wl_settings')) {
            return $defaults;
        }

        $saved = ModuleSetting::pluck('value', 'key')->toArray();

        foreach ($defaults as $key => $val) {
            if (!empty($saved[$key])) {
                $defaults[$key] = $saved[$key];
            }
        }

        return $defaults;
    }

    public function ensureSeeded(): void
    {
        if (!Schema::connection('tenant')->hasTable('wl_topics')) {
            return;
        }

        // 1. Seed Default Settings
        if (ModuleSetting::count() === 0) {
            foreach ($this->getDefaultSettings() as $k => $v) {
                ModuleSetting::setValue($k, $v);
            }
            ModuleSetting::setValue('site_title', 'WonderLearn — Learn • Discover • Grow');
            ModuleSetting::setValue('site_tagline', 'A magical world where learning feels like an adventure!');
        }

        // 2. Seed 6 Explore by Topic Cards
        if (WlTopic::count() === 0) {
            $topics = [
                [
                    'title'            => 'Math Magic',
                    'slug'             => 'math-magic',
                    'card_bg'          => '#FFF4CC',
                    'border_color'     => '#FDE08D',
                    'image'            => '/module-media/WonderLearn/theme/topic-math.png',
                    'description'      => 'Fun number puzzles, counting quests, geometry blocks, and brain-boosting arithmetic adventures!',
                    'age_group'        => 'Ages 4-12',
                    'activities_count' => 42,
                    'sort_order'       => 1,
                ],
                [
                    'title'            => 'Science Lab',
                    'slug'             => 'science-lab',
                    'card_bg'          => '#DFF3FD',
                    'border_color'     => '#BAE6FD',
                    'image'            => '/module-media/WonderLearn/theme/topic-science.png',
                    'description'      => 'Safe virtual experiments, space exploration, bubbling potions, and how the natural world works!',
                    'age_group'        => 'Ages 5-12',
                    'activities_count' => 36,
                    'sort_order'       => 2,
                ],
                [
                    'title'            => 'Reading Land',
                    'slug'             => 'reading-land',
                    'card_bg'          => '#E2F2FE',
                    'border_color'     => '#BFDBFE',
                    'image'            => '/module-media/WonderLearn/theme/topic-reading.png',
                    'description'      => 'Interactive phonics, enchanted fables, vocabulary builders, and read-along audio storybooks!',
                    'age_group'        => 'Ages 4-11',
                    'activities_count' => 58,
                    'sort_order'       => 3,
                ],
                [
                    'title'            => 'Art Studio',
                    'slug'             => 'art-studio',
                    'card_bg'          => '#FCE4EC',
                    'border_color'     => '#FBCFE8',
                    'image'            => '/module-media/WonderLearn/theme/topic-art.png',
                    'description'      => 'Color mixing, creative drawing canvases, origami crafts, and musical rhythm play!',
                    'age_group'        => 'Ages 4-12',
                    'activities_count' => 29,
                    'sort_order'       => 4,
                ],
                [
                    'title'            => 'Geography',
                    'slug'             => 'geography',
                    'card_bg'          => '#ECF8D2',
                    'border_color'     => '#D9F99D',
                    'image'            => '/module-media/WonderLearn/theme/topic-geography.png',
                    'description'      => 'Spin the globe to discover continents, ocean wildlife, world wonders, and cultural festivals!',
                    'age_group'        => 'Ages 6-12',
                    'activities_count' => 31,
                    'sort_order'       => 5,
                ],
                [
                    'title'            => 'Life Skills',
                    'slug'             => 'life-skills',
                    'card_bg'          => '#EAE6FD',
                    'border_color'     => '#DDD6FE',
                    'image'            => '/module-media/WonderLearn/theme/topic-lifeskills.png',
                    'description'      => 'Kindness habits, gardening care, teamwork, emotional intelligence, and everyday problem solving!',
                    'age_group'        => 'Ages 4-12',
                    'activities_count' => 25,
                    'sort_order'       => 6,
                ],
            ];

            foreach ($topics as $topic) {
                WlTopic::create(array_merge($topic, ['is_active' => true]));
            }
        }

        // 3. Seed 4 Magical Stories
        if (WlStory::count() === 0) {
            $stories = [
                [
                    'title'         => 'The Starry Balloon',
                    'slug'          => 'the-starry-balloon',
                    'category_tag'  => 'Friendly',
                    'tag_bg'        => '#E0F2FE',
                    'tag_color'     => '#0369A1',
                    'image'         => '/module-media/WonderLearn/theme/story-starry-balloon.jpg',
                    'summary'       => 'Three best friends board a glowing hot-air balloon and soar through the twinkling midnight constellations.',
                    'story_content' => "Once upon a starry evening, Mia, Leo, and Sam climbed into the basket of a golden hot-air balloon.\n\nAs the gentle night breeze lifted them above the rooftops, the fireflies danced around their lantern. Up in the velvet sky, they met Orion's friendly puppy star, who showed them how constellations connect like glowing dot-to-dot drawings.\n\nBy working together and sharing their compass, the three friends guided the balloon safely back home before sunrise—learning that every journey is brighter when shared with kind friends!",
                    'reading_time'  => '4 min read',
                    'age_range'     => 'Ages 4-8',
                    'is_featured'   => true,
                    'sort_order'    => 1,
                ],
                [
                    'title'         => 'Luna and the Little Clock',
                    'slug'          => 'luna-and-the-little-clock',
                    'category_tag'  => 'Friendship',
                    'tag_bg'        => '#F3E8FF',
                    'tag_color'     => '#7E22CE',
                    'image'         => '/module-media/WonderLearn/theme/story-luna-clock.jpg',
                    'summary'       => 'Under a silver crescent moon, Luna helps a glowing snow-kitten repair the town’s magical chime clock.',
                    'story_content' => "Beneath the silver crescent moon, little Luna heard a soft 'tick-tock-mew!' coming from the garden bench.\n\nA tiny glowing white kitten named Pip was holding a golden gear that had fallen from the Moonbeam Clock. Without it, the morning sun wouldn't know when to wake the flowers!\n\nLuna carefully counted the twelve numbers around the clock face, fitted the little gear right behind the hour hand, and cheered as the chimes rang out across the valley.",
                    'reading_time'  => '5 min read',
                    'age_range'     => 'Ages 5-9',
                    'is_featured'   => true,
                    'sort_order'    => 2,
                ],
                [
                    'title'         => 'The Brave Little Sailor',
                    'slug'          => 'the-brave-little-sailor',
                    'category_tag'  => 'Adventure',
                    'tag_bg'        => '#FCE7F3',
                    'tag_color'     => '#BE185D',
                    'image'         => '/module-media/WonderLearn/theme/story-brave-sailor.jpg',
                    'summary'       => 'Captain Toby hoists his white canvas sail and navigates the sparkling turquoise bay with a playful dolphin crew.',
                    'story_content' => "With his wooden telescope in hand, young Captain Toby set sail across Whispering Wave Bay in his little boat, The Sunbeam.\n\nWhen a sudden gust of wind blew his map overboard, Toby didn't give up. He watched the direction of the seagulls, felt the warm breeze on his sails, and followed a pod of friendly dolphins straight to Rainbow Lighthouse.\n\nToby discovered that true bravery comes from staying calm and curious!",
                    'reading_time'  => '5 min read',
                    'age_range'     => 'Ages 5-10',
                    'is_featured'   => true,
                    'sort_order'    => 3,
                ],
                [
                    'title'         => 'The Secret Garden',
                    'slug'          => 'the-secret-garden',
                    'category_tag'  => 'Nature',
                    'tag_bg'        => '#DCFCE7',
                    'tag_color'     => '#15803D',
                    'image'         => '/module-media/WonderLearn/theme/story-secret-garden.jpg',
                    'summary'       => 'Clara steps through a rose-covered archway and learns how bees, sunlight, and raindrops help a hidden garden bloom.',
                    'story_content' => "Behind an ivy-covered wooden gate, Clara discovered a forgotten garden filled with sleepy flower buds.\n\nEvery afternoon she brought her green watering can, cleared away dry leaves, and watched busy honeybees carry golden pollen from blossom to blossom.\n\nWithin two weeks, the archway burst into pink, lavender, and gold roses—teaching Clara how patience and gentle care bring nature to life.",
                    'reading_time'  => '6 min read',
                    'age_range'     => 'Ages 4-10',
                    'is_featured'   => true,
                    'sort_order'    => 4,
                ],
            ];

            foreach ($stories as $story) {
                WlStory::create(array_merge($story, ['is_active' => true]));
            }
        }

        // 4. Seed 5 Play & Learn Games
        if (WlGame::count() === 0) {
            $games = [
                [
                    'title'           => 'Knight Math Quest: 5 - 2 = ?',
                    'slug'            => 'knight-math-quest',
                    'category'        => 'Math Magic',
                    'game_type'       => 'math',
                    'question_prompt' => 'Sir Leo the Math Knight needs your help to unlock the castle gate! What is 5 - 2?',
                    'correct_answer'  => '3',
                    'options_json'    => ['2', '3', '4', '5'],
                    'image'           => '/module-media/WonderLearn/theme/game-math-knight.jpg',
                    'card_bg'         => '#7DD3FC',
                    'play_count'      => 3420,
                    'sort_order'      => 1,
                ],
                [
                    'title'           => 'ABC Balloon Pop',
                    'slug'            => 'abc-balloon-pop',
                    'category'        => 'Reading Land',
                    'game_type'       => 'balloon',
                    'question_prompt' => 'Pop the correct letter balloon! Which letter comes right after A and B?',
                    'correct_answer'  => 'C',
                    'options_json'    => ['C', 'D', 'E', 'Z'],
                    'image'           => '/module-media/WonderLearn/theme/game-abc-balloons.jpg',
                    'card_bg'         => '#93C5FD',
                    'play_count'      => 2890,
                    'sort_order'      => 2,
                ],
                [
                    'title'           => 'Jigsaw Shape Matcher',
                    'slug'            => 'jigsaw-shape-matcher',
                    'category'        => 'Art Studio',
                    'game_type'       => 'puzzle',
                    'question_prompt' => 'Count the colorful interlocking puzzle pieces in the picture! How many pieces are joining together?',
                    'correct_answer'  => '3 Pieces',
                    'options_json'    => ['2 Pieces', '3 Pieces', '5 Pieces', '6 Pieces'],
                    'image'           => '/module-media/WonderLearn/theme/game-jigsaw-puzzle.jpg',
                    'card_bg'         => '#60A5FA',
                    'play_count'      => 2150,
                    'sort_order'      => 3,
                ],
                [
                    'title'           => 'Atomic Science Lab',
                    'slug'            => 'atomic-science-lab',
                    'category'        => 'Science Lab',
                    'game_type'       => 'science',
                    'question_prompt' => 'Welcome to the Wonder Science Lab! What do plants need along with water and soil to make their food?',
                    'correct_answer'  => 'Sunlight',
                    'options_json'    => ['Sunlight', 'Candy', 'Snowflakes', 'Moon Rocks'],
                    'image'           => '/module-media/WonderLearn/theme/game-science-atom.jpg',
                    'card_bg'         => '#7DD3FC',
                    'play_count'      => 1980,
                    'sort_order'      => 4,
                ],
                [
                    'title'           => 'Star & Heart Memory Cards',
                    'slug'            => 'star-heart-memory-cards',
                    'category'        => 'Life Skills',
                    'game_type'       => 'memory',
                    'question_prompt' => 'Look closely at the two magic cards! Which two symbols are shown on the blue and purple cards?',
                    'correct_answer'  => 'Gold Star & Purple Heart',
                    'options_json'    => ['Gold Star & Purple Heart', 'Moon & Cloud', 'Apple & Banana', 'Crown & Shield'],
                    'image'           => '/module-media/WonderLearn/theme/game-memory-cards.jpg',
                    'card_bg'         => '#6EE7B7',
                    'play_count'      => 2640,
                    'sort_order'      => 5,
                ],
            ];

            foreach ($games as $game) {
                WlGame::create(array_merge($game, ['is_active' => true]));
            }
        }

        // 5. Seed Featured Adventures Carousel
        if (WlAdventure::count() === 0) {
            $adventures = [
                [
                    'title'        => 'Island of Imagination',
                    'slug'         => 'island-of-imagination',
                    'description'  => 'Join Mia, Leo and Ava on a tiny adventure across the island of wonder, puzzles and new friends!',
                    'button_text'  => 'Start Adventure',
                    'button_url'   => '#games',
                    'banner_image' => '/module-media/WonderLearn/theme/adventure-island-banner.jpg',
                    'badge_text'   => 'Island Quest #1',
                    'sort_order'   => 1,
                ],
                [
                    'title'        => 'Sky Castle of Numbers',
                    'slug'         => 'sky-castle-of-numbers',
                    'description'  => 'Sail the flying airship to the Floating Castle and solve fun number riddles with our friendly green dragon!',
                    'button_text'  => 'Start Adventure',
                    'button_url'   => '#explore',
                    'banner_image' => '/module-media/WonderLearn/theme/adventure-island-banner.jpg',
                    'badge_text'   => 'Island Quest #2',
                    'sort_order'   => 2,
                ],
                [
                    'title'        => 'Enchanted Treehouse Library',
                    'slug'         => 'enchanted-treehouse-library',
                    'description'  => 'Explore secret bookshelves with Professor Owl and unlock interactive bedtime stories and badges!',
                    'button_text'  => 'Start Adventure',
                    'button_url'   => '#stories',
                    'banner_image' => '/module-media/WonderLearn/theme/adventure-island-banner.jpg',
                    'badge_text'   => 'Island Quest #3',
                    'sort_order'   => 3,
                ],
            ];

            foreach ($adventures as $adv) {
                WlAdventure::create(array_merge($adv, ['is_active' => true]));
            }
        }

        // 6. Seed 3 Loved by Parents Testimonials
        if (WlTestimonial::count() === 0) {
            $testimonials = [
                [
                    'parent_name'     => 'Sarah M.',
                    'child_age_label' => 'Mom of 6 & 9 yr olds',
                    'quote'           => "WonderLearn has made learning exciting for my kids. Best kids' learning app ever!",
                    'rating'          => 5,
                    'avatar'          => '/module-media/WonderLearn/theme/parent-sarah.jpg',
                    'sort_order'      => 1,
                ],
                [
                    'parent_name'     => 'Daniel K.',
                    'child_age_label' => 'Dad of 7 yr old',
                    'quote'           => 'The stories are beautiful and the games are so much fun! Highly recommended!',
                    'rating'          => 5,
                    'avatar'          => '/module-media/WonderLearn/theme/parent-daniel.jpg',
                    'sort_order'      => 2,
                ],
                [
                    'parent_name'     => 'Priya S.',
                    'child_age_label' => 'Mom of 5 & 8 yr olds',
                    'quote'           => 'Truly a safe, reliable and engaging platform. My kids love it and always ask for more!',
                    'rating'          => 5,
                    'avatar'          => '/module-media/WonderLearn/theme/parent-priya.jpg',
                    'sort_order'      => 3,
                ],
            ];

            foreach ($testimonials as $testi) {
                WlTestimonial::create(array_merge($testi, ['is_active' => true]));
            }
        }

        // 7. Seed Sample Kid Enrollments
        if (WlEnrollment::count() === 0) {
            WlEnrollment::create([
                'parent_name'    => 'Sarah Mitchell',
                'child_name'     => 'Leo Mitchell',
                'child_age'      => 7,
                'email'          => 'sarah.m@example.com',
                'phone'          => '+1 555-0192',
                'favorite_topic' => 'Math Magic',
                'plan_type'      => 'Wonder Explorer Pass',
                'status'         => 'Active',
            ]);
            WlEnrollment::create([
                'parent_name'    => 'Daniel Kim',
                'child_name'     => 'Mia Kim',
                'child_age'      => 6,
                'email'          => 'daniel.k@example.com',
                'phone'          => '+1 555-0148',
                'favorite_topic' => 'Reading Land',
                'plan_type'      => 'Wonder Explorer Pass',
                'status'         => 'Active',
            ]);
        }

        // 8. Seed Standard Module CMS Data (Menus, Pages, Sliders, Media, Videos, Themes, Users)
        if (ModuleMenu::count() === 0) {
            $menus = [
                ['title' => 'Home', 'url' => '#home', 'location' => 'header', 'sort_order' => 1],
                ['title' => 'Explore', 'url' => '#explore', 'location' => 'header', 'sort_order' => 2],
                ['title' => 'Stories', 'url' => '#stories', 'location' => 'header', 'sort_order' => 3],
                ['title' => 'Games', 'url' => '#games', 'location' => 'header', 'sort_order' => 4],
                ['title' => 'About Us', 'url' => '#about', 'location' => 'header', 'sort_order' => 5],
            ];
            foreach ($menus as $m) {
                ModuleMenu::create(array_merge($m, ['target' => '_self', 'is_active' => true]));
            }
        }

        if (ModulePage::count() === 0) {
            ModulePage::create([
                'title'   => 'Child Safety & Ad-Free Promise',
                'slug'    => 'child-safety-promise',
                'content' => 'WonderLearn is 100% kid-safe, COPPA compliant, and completely free of third-party advertisements.',
                'status'  => 'Published',
                'views'   => 1840,
            ]);
            ModulePage::create([
                'title'   => 'Parent & Educator Guide',
                'slug'    => 'parent-educator-guide',
                'content' => 'How our expert-curated curriculum helps children aged 4 to 12 master math, reading, science, and life skills through play.',
                'status'  => 'Published',
                'views'   => 1290,
            ]);
        }

        if (ModuleSlider::count() === 0) {
            ModuleSlider::create([
                'title'       => 'Let Curiosity Lead the Way',
                'subtitle'    => 'A magical world where learning feels like an adventure!',
                'button_text' => 'Start Exploring',
                'button_url'  => '#explore',
                'image'       => '/module-media/WonderLearn/theme/hero-original.jpg',
                'sort_order'  => 1,
                'is_active'   => true,
            ]);
        }

        if (ModuleMedia::count() === 0) {
            $mediaFiles = [
                ['title' => 'WonderLearn Magical Treehouse Hero', 'category' => 'hero', 'file_path' => '/module-media/WonderLearn/theme/hero-original.jpg'],
                ['title' => 'The Starry Balloon Story Art', 'category' => 'stories', 'file_path' => '/module-media/WonderLearn/theme/story-starry-balloon.jpg'],
                ['title' => 'Luna and the Little Clock Art', 'category' => 'stories', 'file_path' => '/module-media/WonderLearn/theme/story-luna-clock.jpg'],
                ['title' => 'Island of Imagination Airship', 'category' => 'adventures', 'file_path' => '/module-media/WonderLearn/theme/adventure-island-original.jpg'],
            ];
            foreach ($mediaFiles as $mf) {
                ModuleMedia::create(array_merge($mf, [
                    'file_type' => 'image/jpeg',
                    'file_size' => '210 KB',
                    'alt_text'  => $mf['title'],
                ]));
            }
        }

        if (ModuleVideo::count() === 0) {
            ModuleVideo::create([
                'title'       => 'Welcome to WonderLearn Island!',
                'description' => 'Take a magical tour through Math Magic, Science Lab, and Reading Land.',
                'category'    => 'Adventure Tour',
                'video_url'   => 'https://www.youtube.com/embed/ScMzIvxBSi4',
                'thumbnail'   => '/module-media/WonderLearn/theme/story-starry-balloon.jpg',
                'duration'    => '02:30',
                'sort_order'  => 1,
                'is_active'   => true,
            ]);
        }

        if (ModuleTheme::count() === 0) {
            ModuleTheme::create([
                'name'            => 'WonderLearn Magical Sky Theme',
                'slug'            => 'wonderlearn-magical-sky',
                'primary_color'   => '#1D58B8',
                'secondary_color' => '#14884B',
                'font_family'     => 'Fredoka & Nunito',
                'layout_style'    => 'Storybook Cloud & Emerald Hill',
                'thumbnail'       => '/module-media/WonderLearn/theme/wonderlearn-full-preview.jpg',
                'is_active'       => true,
            ]);
        }

        if (ModuleUser::count() === 0) {
            ModuleUser::create([
                'name'      => 'Emma Watson (Curriculum Director)',
                'email'     => 'emma@wonderlearn.com',
                'phone'     => '+1 555-2300',
                'role_name' => 'Administrator',
                'status'    => 'Active',
            ]);
            ModuleUser::create([
                'name'      => 'Liam Carter (Children Story Author)',
                'email'     => 'liam@wonderlearn.com',
                'phone'     => '+1 555-2301',
                'role_name' => 'Editor',
                'status'    => 'Active',
            ]);
        }
    }
}
