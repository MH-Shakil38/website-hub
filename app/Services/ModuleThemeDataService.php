<?php

namespace App\Services;

use App\Support\ModuleCmsResolver;
use Illuminate\Support\Facades\Schema;

class ModuleThemeDataService
{
    /**
     * Load the default theme config for a module (Modules/{Module}/Config/defaults.php).
     */
    public function getDefaults(string $module = 'Interior'): array
    {
        $path = base_path("Modules/{$module}/Config/defaults.php");
        return file_exists($path) ? (require $path) : [];
    }

    /**
     * Load only the user's saved custom JSON overrides from {prefix}settings (without base64 defaults).
     */
    public function getCustomSections(string $module = 'Interior'): array
    {
        try {
            $meta = ModuleCmsResolver::moduleMeta($module);
            $prefix = $meta['prefix'];
            if (!Schema::hasTable($prefix . 'settings')) {
                return [];
            }
            $settingClass = ModuleCmsResolver::settingModel($module);
            $raw = $settingClass::getValue('theme_sections_json', '');
            if (!empty($raw)) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    return $this->stripBase64Assets($decoded);
                }
            }
        } catch (\Throwable $e) {
            // Return empty array on error
        }

        return [];
    }

    /**
     * Strip embedded data:image/... base64 default strings before saving to database
     * so the database only stores lightweight custom text and uploaded file URLs.
     */
    public function stripBase64Assets(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = $this->stripBase64Assets($v);
            } elseif (is_string($v) && str_starts_with($v, 'data:image/')) {
                unset($data[$k]);
            }
        }
        return $data;
    }

    /**
     * Resolve complete frontend data for a module by merging database customizations
     * on top of Modules/{Module}/Config/defaults.php.
     * Any field or image left blank automatically falls back to the original default!
     */
    public function getFrontendData(string $module = 'Interior'): array
    {
        $data = $this->getDefaults($module);

        try {
            $meta = ModuleCmsResolver::moduleMeta($module);
            $prefix = $meta['prefix'];

            if (!Schema::hasTable($prefix . 'settings')) {
                return $data;
            }

            $settingClass = ModuleCmsResolver::settingModel($module);
            $savedSettings = $settingClass::pluck('value', 'key')->toArray();

            // 1. Merge JSON section overrides stored via the Visual Website Editor
            if (!empty($savedSettings['theme_sections_json'])) {
                $customSections = json_decode($savedSettings['theme_sections_json'], true);
                if (is_array($customSections)) {
                    $data = $this->mergeNonEmpty($data, $customSections);
                }
            }

            // 2. Apply basic Settings overrides ONLY if user explicitly customized them via Visual Editor
            if (!empty($savedSettings['custom_brand_applied'])) {
                if (!empty($savedSettings['site_title'])) {
                    $data['meta']['page_title'] = $savedSettings['site_title'];
                }
                if (!empty($savedSettings['module_logo'])) {
                    $data['header']['logo'] = $savedSettings['module_logo'];
                    $data['footer']['logo'] = $savedSettings['module_logo'];
                }
                if (!empty($savedSettings['contact_phone'])) {
                    $data['footer']['phone'] = $savedSettings['contact_phone'];
                }
                if (!empty($savedSettings['contact_address'])) {
                    $data['footer']['address'] = $savedSettings['contact_address'];
                }
                if (!empty($savedSettings['footer_copyright'])) {
                    $data['footer']['copyright'] = $savedSettings['footer_copyright'];
                }
            }

            // 3. Featured Projects from {prefix}projects (if user added custom projects)
            $projectModelClass = "Modules\\{$module}\\App\\Models\\Project";
            if (class_exists($projectModelClass) && Schema::hasTable($prefix . 'projects')) {
                $dbProjects = $projectModelClass::where('is_active', true)->orderBy('id')->get();
                if ($dbProjects->isNotEmpty()) {
                    $customProjItems = [];
                    foreach ($dbProjects as $idx => $p) {
                        $fallbackImg = $data['projects']['items'][$idx % count($data['projects']['items'])]['image'] ?? '';
                        $customProjItems[] = [
                            'title' => $p->title,
                            'location' => $p->category ?: 'Chattogram',
                            'image' => !empty($p->image) ? $p->image : $fallbackImg,
                        ];
                    }
                    $data['projects']['items'] = $customProjItems;
                }
            }

            // 4. Custom Photo Gallery items from {prefix}media (if user added custom gallery photos)
            if (!empty($savedSettings['use_custom_gallery']) && Schema::hasTable($prefix . 'media')) {
                $mediaClass = ModuleCmsResolver::mediaModel($module);
                $dbMedia = $mediaClass::latest('id')->get();
                if ($dbMedia->isNotEmpty()) {
                    $customGal = [];
                    foreach ($dbMedia as $m) {
                        $customGal[] = [
                            'src' => $m->file_path,
                            'full' => $m->file_path,
                            'cat' => $m->category ?? 'living',
                            't' => $m->title,
                        ];
                    }
                    // Prepend custom uploaded gallery photos before default gallery items
                    $data['gallery']['items'] = array_merge($customGal, $data['gallery']['items']);
                }
            }

            // 5. Video Gallery items from {prefix}videos (if user added custom videos)
            $videoModelClass = "Modules\\{$module}\\App\\Models\\ModuleVideo";
            if (class_exists($videoModelClass) && Schema::hasTable($prefix . 'videos')) {
                $dbVideos = $videoModelClass::where('is_active', true)->orderBy('sort_order')->latest('id')->get();
                if ($dbVideos->isNotEmpty()) {
                    $customVideos = [];
                    foreach ($dbVideos as $v) {
                        $customVideos[] = [
                            'title' => $v->title,
                            'category' => $v->category ?: 'walkthrough',
                            'duration' => $v->duration ?: '02:30',
                            'thumbnail' => $v->thumbnail ?: 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=800&q=80',
                            'video_url' => $this->normalizeVideoUrl($v->video_url),
                        ];
                    }
                    $data['videos']['items'] = $customVideos;
                }
            }
        } catch (\Throwable $e) {
            // Always safely return defaults on any DB exception
        }

        return $data;
    }

    /**
     * Convert normal YouTube watch/short links into embeddable URLs automatically for non-tech users.
     */
    public function normalizeVideoUrl(string $url): string
    {
        $url = trim($url);
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        if (preg_match('/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        return $url;
    }

    /**
     * Recursively merge arrays, ignoring null or empty-string overrides so defaults stay intact.
     * Supports dynamic repeatable item lists (e.g. services.items) where users can add more items.
     */
    protected function mergeNonEmpty(array $defaults, array $custom): array
    {
        foreach ($custom as $key => $val) {
            // Dynamic repeatable list (numeric array of item arrays, e.g. 'items' in services)
            if ($key === 'items' && is_array($val) && isset($defaults[$key]) && is_array($defaults[$key]) && !empty($defaults[$key])) {
                $defaultTemplates = array_values($defaults[$key]);
                $templateCount = count($defaultTemplates);
                $mergedItems = [];

                foreach (array_values($val) as $idx => $customItem) {
                    $fallbackTemplate = $defaultTemplates[$idx % $templateCount];
                    if (is_array($customItem) && is_array($fallbackTemplate)) {
                        $mergedItems[$idx] = $this->mergeNonEmpty($fallbackTemplate, $customItem);
                    } else {
                        $mergedItems[$idx] = ($customItem !== null && $customItem !== '') ? $customItem : $fallbackTemplate;
                    }
                }

                if (!empty($mergedItems)) {
                    $defaults[$key] = $mergedItems;
                }
            } elseif (is_array($val) && isset($defaults[$key]) && is_array($defaults[$key])) {
                $defaults[$key] = $this->mergeNonEmpty($defaults[$key], $val);
            } elseif ($val !== null && $val !== '') {
                $defaults[$key] = $val;
            }
        }
        return $defaults;
    }
}
