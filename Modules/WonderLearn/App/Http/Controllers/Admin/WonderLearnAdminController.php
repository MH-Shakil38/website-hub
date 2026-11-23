<?php

namespace Modules\WonderLearn\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ModuleManagerService;
use App\Support\ModuleCmsResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\WonderLearn\App\Models\ModuleSetting;
use Modules\WonderLearn\App\Models\WlAdventure;
use Modules\WonderLearn\App\Models\WlEnrollment;
use Modules\WonderLearn\App\Models\WlGame;
use Modules\WonderLearn\App\Models\WlStory;
use Modules\WonderLearn\App\Models\WlSubscriber;
use Modules\WonderLearn\App\Models\WlTestimonial;
use Modules\WonderLearn\App\Models\WlTopic;
use Modules\WonderLearn\App\Services\WonderLearnDataService;

class WonderLearnAdminController extends Controller
{
    public function __construct(
        protected WonderLearnDataService $dataService
    ) {}

    public function dashboard()
    {
        $this->dataService->ensureSeeded();

        $topicsCount       = WlTopic::count();
        $storiesCount      = WlStory::count();
        $gamesCount        = WlGame::count();
        $adventuresCount   = WlAdventure::count();
        $testimonialsCount = WlTestimonial::count();
        $enrollmentsCount  = WlEnrollment::count();
        $subscribersCount  = WlSubscriber::count();

        $recentStories     = WlStory::latest('id')->take(4)->get();
        $recentEnrollments = WlEnrollment::latest('id')->take(5)->get();

        return view('wonderlearn::admin.dashboard', compact(
            'topicsCount',
            'storiesCount',
            'gamesCount',
            'adventuresCount',
            'testimonialsCount',
            'enrollmentsCount',
            'subscribersCount',
            'recentStories',
            'recentEnrollments'
        ));
    }

    // =========================================================================
    // 1. EXPLORE BY TOPIC CRUD (wl_topics)
    // =========================================================================
    public function topics()
    {
        $this->dataService->ensureSeeded();
        $topics = WlTopic::orderBy('sort_order')->orderBy('id')->get();

        return view('wonderlearn::admin.topics', compact('topics'));
    }

    public function storeTopic(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:120',
            'card_bg'          => 'nullable|string|max:30',
            'border_color'     => 'nullable|string|max:30',
            'description'      => 'nullable|string|max:500',
            'age_group'        => 'nullable|string|max:50',
            'activities_count' => 'nullable|integer|min:1',
            'sort_order'       => 'nullable|integer',
            'image_file'       => 'nullable|image|max:4096',
        ]);

        $imageUrl = '/module-media/WonderLearn/theme/topic-math.png';
        if ($request->hasFile('image_file')) {
            $imageUrl = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'topics', 'WonderLearn');
        }

        $topic = WlTopic::create([
            'title'            => $validated['title'],
            'slug'             => Str::slug($validated['title']) . '-' . Str::random(3),
            'card_bg'          => $validated['card_bg'] ?: '#FFF4CC',
            'border_color'     => $validated['border_color'] ?: '#FDE08D',
            'image'            => $imageUrl,
            'description'      => $validated['description'] ?? null,
            'age_group'        => $validated['age_group'] ?: 'Ages 4-12',
            'activities_count' => $validated['activities_count'] ?? 25,
            'sort_order'       => $validated['sort_order'] ?? (WlTopic::max('sort_order') + 1),
            'is_active'        => true,
        ]);

        ActivityLog::record('WonderLearn topic added', "{$topic->title} [wl_topics]", 'bi-grid-3x3-gap-fill', '#FEF3C7', '#D97706');

        return back()->with('success', "Topic [{$topic->title}] created successfully.");
    }

    public function updateTopic(Request $request, int $id)
    {
        $topic = WlTopic::findOrFail($id);

        $validated = $request->validate([
            'title'            => 'required|string|max:120',
            'card_bg'          => 'nullable|string|max:30',
            'border_color'     => 'nullable|string|max:30',
            'description'      => 'nullable|string|max:500',
            'age_group'        => 'nullable|string|max:50',
            'activities_count' => 'nullable|integer|min:1',
            'sort_order'       => 'nullable|integer',
            'image_file'       => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image_file')) {
            $topic->image = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'topics', 'WonderLearn');
        }

        $topic->update([
            'title'            => $validated['title'],
            'card_bg'          => $validated['card_bg'] ?: $topic->card_bg,
            'border_color'     => $validated['border_color'] ?: $topic->border_color,
            'description'      => $validated['description'] ?? $topic->description,
            'age_group'        => $validated['age_group'] ?: $topic->age_group,
            'activities_count' => $validated['activities_count'] ?? $topic->activities_count,
            'sort_order'       => $validated['sort_order'] ?? $topic->sort_order,
            'is_active'        => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Topic [{$topic->title}] updated successfully.");
    }

    public function deleteTopic(int $id)
    {
        WlTopic::findOrFail($id)->delete();

        return back()->with('success', 'Topic removed from wl_topics.');
    }

    // =========================================================================
    // 2. MAGICAL STORIES CRUD (wl_stories)
    // =========================================================================
    public function stories()
    {
        $this->dataService->ensureSeeded();
        $stories = WlStory::orderBy('sort_order')->orderBy('id')->get();

        return view('wonderlearn::admin.stories', compact('stories'));
    }

    public function storeStory(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:160',
            'category_tag'  => 'required|string|max:60',
            'tag_bg'        => 'nullable|string|max:30',
            'tag_color'     => 'nullable|string|max:30',
            'summary'       => 'nullable|string|max:500',
            'story_content' => 'nullable|string',
            'reading_time'  => 'nullable|string|max:40',
            'age_range'     => 'nullable|string|max:40',
            'sort_order'    => 'nullable|integer',
            'image_file'    => 'nullable|image|max:4096',
        ]);

        $imageUrl = '/module-media/WonderLearn/theme/story-starry-balloon.jpg';
        if ($request->hasFile('image_file')) {
            $imageUrl = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'stories', 'WonderLearn');
        }

        $story = WlStory::create([
            'title'         => $validated['title'],
            'slug'          => Str::slug($validated['title']) . '-' . Str::random(3),
            'category_tag'  => $validated['category_tag'],
            'tag_bg'        => $validated['tag_bg'] ?: '#E0F2FE',
            'tag_color'     => $validated['tag_color'] ?: '#0369A1',
            'image'         => $imageUrl,
            'summary'       => $validated['summary'] ?? null,
            'story_content' => $validated['story_content'] ?? $validated['summary'],
            'reading_time'  => $validated['reading_time'] ?: '5 min read',
            'age_range'     => $validated['age_range'] ?: 'Ages 4-10',
            'is_featured'   => true,
            'sort_order'    => $validated['sort_order'] ?? (WlStory::max('sort_order') + 1),
            'is_active'     => true,
        ]);

        ActivityLog::record('Magical story published', "{$story->title} [wl_stories]", 'bi-book-half', '#DBEAFE', '#2563EB');

        return back()->with('success', "Story [{$story->title}] created successfully.");
    }

    public function updateStory(Request $request, int $id)
    {
        $story = WlStory::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'required|string|max:160',
            'category_tag'  => 'required|string|max:60',
            'tag_bg'        => 'nullable|string|max:30',
            'tag_color'     => 'nullable|string|max:30',
            'summary'       => 'nullable|string|max:500',
            'story_content' => 'nullable|string',
            'reading_time'  => 'nullable|string|max:40',
            'age_range'     => 'nullable|string|max:40',
            'sort_order'    => 'nullable|integer',
            'image_file'    => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image_file')) {
            $story->image = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'stories', 'WonderLearn');
        }

        $story->update([
            'title'         => $validated['title'],
            'category_tag'  => $validated['category_tag'],
            'tag_bg'        => $validated['tag_bg'] ?: $story->tag_bg,
            'tag_color'     => $validated['tag_color'] ?: $story->tag_color,
            'summary'       => $validated['summary'] ?? $story->summary,
            'story_content' => $validated['story_content'] ?? $story->story_content,
            'reading_time'  => $validated['reading_time'] ?: $story->reading_time,
            'age_range'     => $validated['age_range'] ?: $story->age_range,
            'sort_order'    => $validated['sort_order'] ?? $story->sort_order,
            'is_active'     => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Story [{$story->title}] updated successfully.");
    }

    public function deleteStory(int $id)
    {
        WlStory::findOrFail($id)->delete();

        return back()->with('success', 'Story deleted from wl_stories.');
    }

    // =========================================================================
    // 3. PLAY & LEARN GAMES CRUD (wl_games)
    // =========================================================================
    public function games()
    {
        $this->dataService->ensureSeeded();
        $games = WlGame::orderBy('sort_order')->orderBy('id')->get();

        return view('wonderlearn::admin.games', compact('games'));
    }

    public function storeGame(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:160',
            'category'        => 'required|string|max:80',
            'question_prompt' => 'nullable|string|max:400',
            'correct_answer'  => 'nullable|string|max:120',
            'options_csv'     => 'nullable|string|max:300',
            'sort_order'      => 'nullable|integer',
            'image_file'      => 'nullable|image|max:4096',
        ]);

        $imageUrl = '/module-media/WonderLearn/theme/game-math-knight.jpg';
        if ($request->hasFile('image_file')) {
            $imageUrl = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'games', 'WonderLearn');
        }

        $options = !empty($validated['options_csv'])
            ? array_values(array_filter(array_map('trim', explode(',', $validated['options_csv']))))
            : ['2', '3', '4', '5'];

        $game = WlGame::create([
            'title'           => $validated['title'],
            'slug'            => Str::slug($validated['title']) . '-' . Str::random(3),
            'category'        => $validated['category'],
            'game_type'       => 'quiz',
            'question_prompt' => $validated['question_prompt'] ?: 'Solve the fun learning challenge!',
            'correct_answer'  => $validated['correct_answer'] ?: ($options[0] ?? '3'),
            'options_json'    => $options,
            'image'           => $imageUrl,
            'sort_order'      => $validated['sort_order'] ?? (WlGame::max('sort_order') + 1),
            'is_active'       => true,
        ]);

        ActivityLog::record('Play & Learn game added', "{$game->title} [wl_games]", 'bi-controller', '#DCFCE7', '#16A34A');

        return back()->with('success', "Game [{$game->title}] created successfully.");
    }

    public function updateGame(Request $request, int $id)
    {
        $game = WlGame::findOrFail($id);

        $validated = $request->validate([
            'title'           => 'required|string|max:160',
            'category'        => 'required|string|max:80',
            'question_prompt' => 'nullable|string|max:400',
            'correct_answer'  => 'nullable|string|max:120',
            'options_csv'     => 'nullable|string|max:300',
            'sort_order'      => 'nullable|integer',
            'image_file'      => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image_file')) {
            $game->image = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'games', 'WonderLearn');
        }

        $options = !empty($validated['options_csv'])
            ? array_values(array_filter(array_map('trim', explode(',', $validated['options_csv']))))
            : $game->options_json;

        $game->update([
            'title'           => $validated['title'],
            'category'        => $validated['category'],
            'question_prompt' => $validated['question_prompt'] ?: $game->question_prompt,
            'correct_answer'  => $validated['correct_answer'] ?: $game->correct_answer,
            'options_json'    => $options,
            'sort_order'      => $validated['sort_order'] ?? $game->sort_order,
            'is_active'       => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Game [{$game->title}] updated successfully.");
    }

    public function deleteGame(int $id)
    {
        WlGame::findOrFail($id)->delete();

        return back()->with('success', 'Game deleted from wl_games.');
    }

    // =========================================================================
    // 4. FEATURED ADVENTURES CRUD (wl_adventures)
    // =========================================================================
    public function adventures()
    {
        $this->dataService->ensureSeeded();
        $adventures = WlAdventure::orderBy('sort_order')->orderBy('id')->get();

        return view('wonderlearn::admin.adventures', compact('adventures'));
    }

    public function storeAdventure(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:160',
            'description' => 'required|string|max:500',
            'button_text' => 'nullable|string|max:60',
            'button_url'  => 'nullable|string|max:200',
            'sort_order'  => 'nullable|integer',
            'image_file'  => 'nullable|image|max:4096',
        ]);

        $bannerUrl = '/module-media/WonderLearn/theme/adventure-island-banner.jpg';
        if ($request->hasFile('image_file')) {
            $bannerUrl = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'adventures', 'WonderLearn');
        }

        $adv = WlAdventure::create([
            'title'        => $validated['title'],
            'slug'         => Str::slug($validated['title']) . '-' . Str::random(3),
            'description'  => $validated['description'],
            'button_text'  => $validated['button_text'] ?: 'Start Adventure',
            'button_url'   => $validated['button_url'] ?: '#games',
            'banner_image' => $bannerUrl,
            'sort_order'   => $validated['sort_order'] ?? (WlAdventure::max('sort_order') + 1),
            'is_active'    => true,
        ]);

        return back()->with('success', "Adventure [{$adv->title}] created successfully.");
    }

    public function updateAdventure(Request $request, int $id)
    {
        $adv = WlAdventure::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:160',
            'description' => 'required|string|max:500',
            'button_text' => 'nullable|string|max:60',
            'button_url'  => 'nullable|string|max:200',
            'sort_order'  => 'nullable|integer',
            'image_file'  => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image_file')) {
            $adv->banner_image = ModuleCmsResolver::storeModuleFile($request->file('image_file'), 'adventures', 'WonderLearn');
        }

        $adv->update([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'button_text' => $validated['button_text'] ?: $adv->button_text,
            'button_url'  => $validated['button_url'] ?: $adv->button_url,
            'sort_order'  => $validated['sort_order'] ?? $adv->sort_order,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Adventure [{$adv->title}] updated successfully.");
    }

    public function deleteAdventure(int $id)
    {
        WlAdventure::findOrFail($id)->delete();

        return back()->with('success', 'Adventure deleted from wl_adventures.');
    }

    // =========================================================================
    // 5. LOVED BY PARENTS TESTIMONIALS CRUD (wl_testimonials)
    // =========================================================================
    public function testimonials()
    {
        $this->dataService->ensureSeeded();
        $testimonials = WlTestimonial::orderBy('sort_order')->orderBy('id')->get();

        return view('wonderlearn::admin.testimonials', compact('testimonials'));
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'parent_name'     => 'required|string|max:100',
            'child_age_label' => 'nullable|string|max:100',
            'quote'           => 'required|string|max:500',
            'rating'          => 'nullable|integer|min:1|max:5',
            'sort_order'      => 'nullable|integer',
            'avatar_file'     => 'nullable|image|max:4096',
        ]);

        $avatarUrl = '/module-media/WonderLearn/theme/parent-sarah.jpg';
        if ($request->hasFile('avatar_file')) {
            $avatarUrl = ModuleCmsResolver::storeModuleFile($request->file('avatar_file'), 'parents', 'WonderLearn');
        }

        WlTestimonial::create([
            'parent_name'     => $validated['parent_name'],
            'child_age_label' => $validated['child_age_label'] ?: 'WonderLearn Parent',
            'quote'           => $validated['quote'],
            'rating'          => $validated['rating'] ?? 5,
            'avatar'          => $avatarUrl,
            'sort_order'      => $validated['sort_order'] ?? (WlTestimonial::max('sort_order') + 1),
            'is_active'       => true,
        ]);

        return back()->with('success', 'Parent review added successfully.');
    }

    public function updateTestimonial(Request $request, int $id)
    {
        $testi = WlTestimonial::findOrFail($id);

        $validated = $request->validate([
            'parent_name'     => 'required|string|max:100',
            'child_age_label' => 'nullable|string|max:100',
            'quote'           => 'required|string|max:500',
            'rating'          => 'nullable|integer|min:1|max:5',
            'sort_order'      => 'nullable|integer',
            'avatar_file'     => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('avatar_file')) {
            $testi->avatar = ModuleCmsResolver::storeModuleFile($request->file('avatar_file'), 'parents', 'WonderLearn');
        }

        $testi->update([
            'parent_name'     => $validated['parent_name'],
            'child_age_label' => $validated['child_age_label'] ?: $testi->child_age_label,
            'quote'           => $validated['quote'],
            'rating'          => $validated['rating'] ?? $testi->rating,
            'sort_order'      => $validated['sort_order'] ?? $testi->sort_order,
            'is_active'       => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Parent review updated successfully.');
    }

    public function deleteTestimonial(int $id)
    {
        WlTestimonial::findOrFail($id)->delete();

        return back()->with('success', 'Parent review deleted.');
    }

    // =========================================================================
    // 6. KID ENROLLMENTS & SUBSCRIBERS (wl_enrollments & wl_subscribers)
    // =========================================================================
    public function enrollments()
    {
        $this->dataService->ensureSeeded();
        $enrollments = WlEnrollment::latest('id')->get();
        $subscribers = WlSubscriber::latest('id')->get();

        return view('wonderlearn::admin.enrollments', compact('enrollments', 'subscribers'));
    }

    public function storeEnrollment(Request $request)
    {
        $validated = $request->validate([
            'parent_name'    => 'required|string|max:120',
            'child_name'     => 'required|string|max:120',
            'child_age'      => 'required|integer|min:3|max:14',
            'email'          => 'required|email|max:150',
            'phone'          => 'nullable|string|max:40',
            'favorite_topic' => 'nullable|string|max:80',
            'status'         => 'nullable|string|max:40',
        ]);

        WlEnrollment::create(array_merge($validated, [
            'plan_type' => 'Wonder Explorer Pass',
            'status'    => $validated['status'] ?? 'Active',
        ]));

        return back()->with('success', 'Kid enrollment added successfully.');
    }

    public function updateEnrollment(Request $request, int $id)
    {
        $enrollment = WlEnrollment::findOrFail($id);
        $validated = $request->validate([
            'parent_name'    => 'required|string|max:120',
            'child_name'     => 'required|string|max:120',
            'child_age'      => 'required|integer|min:3|max:14',
            'email'          => 'required|email|max:150',
            'phone'          => 'nullable|string|max:40',
            'favorite_topic' => 'nullable|string|max:80',
            'status'         => 'required|string|max:40',
        ]);

        $enrollment->update($validated);

        return back()->with('success', 'Enrollment updated successfully.');
    }

    public function deleteEnrollment(int $id)
    {
        WlEnrollment::findOrFail($id)->delete();

        return back()->with('success', 'Enrollment deleted.');
    }

    public function deleteSubscriber(int $id)
    {
        WlSubscriber::findOrFail($id)->delete();

        return back()->with('success', 'Subscriber removed.');
    }

    // =========================================================================
    // 7. THEME & HERO SETTINGS (wl_settings)
    // =========================================================================
    public function settings()
    {
        $this->dataService->ensureSeeded();
        $s = $this->dataService->getSettingsData();

        return view('wonderlearn::admin.settings', compact('s'));
    }

    public function saveSettings(Request $request)
    {
        $textKeys = [
            'brand_name',
            'brand_tagline',
            'login_text',
            'join_text',
            'hero_title_line1',
            'hero_title_line2',
            'hero_subtitle',
            'hero_btn_text',
            'trust_badge_1',
            'trust_badge_2',
            'trust_badge_3',
            'trust_badge_4',
            'topics_heading',
            'stories_heading',
            'stories_btn_text',
            'games_heading',
            'games_btn_text',
            'adventures_heading',
            'parents_heading',
            'app_heading',
            'app_subtext',
            'footer_tagline',
            'newsletter_title',
            'newsletter_subtitle',
            'contact_email',
            'contact_phone',
        ];

        foreach ($textKeys as $k) {
            if ($request->has($k)) {
                ModuleSetting::setValue($k, $request->input($k));
            }
        }

        if ($request->hasFile('hero_bg_file')) {
            $heroUrl = ModuleCmsResolver::storeModuleFile($request->file('hero_bg_file'), 'hero', 'WonderLearn');
            ModuleSetting::setValue('hero_bg_image', $heroUrl);
        }

        if ($request->hasFile('logo_icon_file')) {
            $logoUrl = ModuleCmsResolver::storeModuleFile($request->file('logo_icon_file'), 'branding', 'WonderLearn');
            ModuleSetting::setValue('logo_icon', $logoUrl);
        }

        ActivityLog::record('WonderLearn theme settings updated', 'Saved in wl_settings', 'bi-palette2', '#FEF3C7', '#D97706');

        return back()->with('success', 'WonderLearn theme & hero settings saved successfully!');
    }

    public function resetSettings(ModuleManagerService $manager)
    {
        $manager->resetModuleToDefault('WonderLearn');

        return back()->with('success', 'WonderLearn module restored 100% to original default design & content!');
    }
}
