@extends('admin.layouts.master')

@php
    $currentTab = $activeTab ?? request()->query('tab', 'hero');
    $tabsMeta = [
        'hero'    => ['title' => 'Hero Banner & Developer Portrait', 'sub' => 'Manage hero headline, badge, cutout developer portrait, CV file, and script callout.'],
        'about'   => ['title' => 'About Me, Bio & Statistics', 'sub' => 'Manage biography, workspace photo, 3 key counters, and 4 highlight cards.'],
        'process' => ['title' => 'Why Work With Me & 7-Step Process', 'sub' => 'Customize your 6 core strengths and 7-step software engineering workflow.'],
        'general' => ['title' => 'Theme Mode (Light/Dark), Visibility, Contact & SEO', 'sub' => 'Switch default Light/Dark theme mode, toggle section visibility, update contact info & SEO metadata.'],
    ];
    $info = $tabsMeta[$currentTab] ?? $tabsMeta['hero'];
@endphp

@section('title', $info['title'] . ' — Developer Portfolio Admin')

@section('admin_content')
    <!-- Top Header Card -->
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">{{ $info['title'] }}</h3>
                    <span class="sb-badge-pill sb-badge-blue">port_settings</span>
                </div>
                <p class="text-muted small mb-0">{{ $info['sub'] }}</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('/developer-portfolio') }}" target="_blank" class="btn btn-outline-dark btn-sm fw-semibold px-3 py-2 rounded-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Live Portfolio
                </a>
                <form action="{{ route('admin.portfolio.settings.reset') }}" method="POST" onsubmit="return confirm('Reset Portfolio module back to 100% original default design & seeded items?')">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm fw-bold px-3 py-2 rounded-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Defaults
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Section Switcher Tabs -->
        <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
            <a href="{{ route('admin.portfolio.settings', ['tab' => 'hero']) }}" class="btn btn-sm {{ $currentTab === 'hero' ? 'btn-primary' : 'btn-light' }} rounded-pill px-3 fw-semibold">
                <i class="bi bi-person-bounding-box me-1"></i> Hero &amp; Portrait
            </a>
            <a href="{{ route('admin.portfolio.settings', ['tab' => 'about']) }}" class="btn btn-sm {{ $currentTab === 'about' ? 'btn-primary' : 'btn-light' }} rounded-pill px-3 fw-semibold">
                <i class="bi bi-person-vcard me-1"></i> About Me &amp; Stats
            </a>
            <a href="{{ route('admin.portfolio.settings', ['tab' => 'process']) }}" class="btn btn-sm {{ $currentTab === 'process' ? 'btn-primary' : 'btn-light' }} rounded-pill px-3 fw-semibold">
                <i class="bi bi-diagram-3 me-1"></i> Why Me &amp; 7-Step Process
            </a>
            <a href="{{ route('admin.portfolio.settings', ['tab' => 'general']) }}" class="btn btn-sm {{ $currentTab === 'general' ? 'btn-primary' : 'btn-light' }} rounded-pill px-3 fw-semibold">
                <i class="bi bi-circle-half me-1"></i> Theme (Light/Dark), Visibility &amp; SEO
            </a>
        </div>
    </div>

    <form action="{{ route('admin.portfolio.settings.save') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if($currentTab === 'hero')
            <div class="sb-table-card p-4">
                <div class="row g-4">
                    <div class="col-lg-4">
                        @include('admin.partials.image-dropzone', [
                            'name'    => 'hero_portrait_image',
                            'label'   => 'Developer Cutout Portrait (PNG/JPG)',
                            'preview' => $t['hero']['portrait_image'] ?? '/module-media/Portfolio/theme/hero-developer-cutout.png',
                            'height'  => '280px',
                            'fit'     => 'contain',
                            'hint'    => 'Drop new developer cutout photo here',
                        ])

                        <div class="mt-3 p-3 rounded-3 border bg-light">
                            <label class="form-label fw-bold small">Upload New Downloadable CV (PDF)</label>
                            <input type="file" name="hero_cv_file" accept=".pdf,.doc,.docx" class="form-control form-control-sm mb-2">
                            <small class="text-muted d-block">Current CV: <a href="{{ $t['hero']['btn_cv_url'] ?? '#' }}" target="_blank">Download Current CV</a></small>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-primary">Header Logo Brand Name (Menu Name)</label>
                                <input type="text" name="sections[header][brand_name]" value="{{ $t['header']['brand_name'] ?? 'Alex Morgan' }}" class="form-control" placeholder="e.g. Alex Morgan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-primary">Header Brand Subtitle (Under Name)</label>
                                <input type="text" name="sections[header][brand_sub]" value="{{ $t['header']['brand_sub'] ?? 'Web Developer' }}" class="form-control" placeholder="e.g. Web Developer">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Introduction Badge</label>
                                <input type="text" name="sections[hero][badge]" value="{{ $t['hero']['badge'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Headline Line 1</label>
                                <input type="text" name="sections[hero][title_line_1]" value="{{ $t['hero']['title_line_1'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Headline Line 2</label>
                                <input type="text" name="sections[hero][title_line_2]" value="{{ $t['hero']['title_line_2'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Headline Line 3</label>
                                <input type="text" name="sections[hero][title_line_3]" value="{{ $t['hero']['title_line_3'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Electric Blue Highlight Word</label>
                                <input type="text" name="sections[hero][title_highlight]" value="{{ $t['hero']['title_highlight'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Hero Short Description</label>
                                <textarea name="sections[hero][description]" rows="2" class="form-control">{{ $t['hero']['description'] ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Primary Button Text</label>
                                <input type="text" name="sections[hero][btn_work_text]" value="{{ $t['hero']['btn_work_text'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">CV Button Text</label>
                                <input type="text" name="sections[hero][btn_cv_text]" value="{{ $t['hero']['btn_cv_text'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Script Callout Word 1</label>
                                <input type="text" name="sections[hero][script_line_1]" value="{{ $t['hero']['script_line_1'] ?? 'Code' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Script Callout Word 2</label>
                                <input type="text" name="sections[hero][script_line_2]" value="{{ $t['hero']['script_line_2'] ?? 'Innovate' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Script Callout Word 3</label>
                                <input type="text" name="sections[hero][script_line_3]" value="{{ $t['hero']['script_line_3'] ?? 'Create' }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($currentTab === 'about')
            <div class="sb-table-card p-4">
                <div class="row g-4">
                    <div class="col-lg-4">
                        @include('admin.partials.image-dropzone', [
                            'name'    => 'about_workspace_image',
                            'label'   => 'About Me Workspace Photo',
                            'preview' => $t['about']['workspace_image'] ?? '/module-media/Portfolio/theme/about-workspace.jpg',
                            'height'  => '240px',
                            'fit'     => 'cover',
                        ])
                    </div>
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Heading Line 1</label>
                                <input type="text" name="sections[about][heading_line_1]" value="{{ $t['about']['heading_line_1'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Heading Line 2</label>
                                <input type="text" name="sections[about][heading_line_2]" value="{{ $t['about']['heading_line_2'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Italic Blue Highlight</label>
                                <input type="text" name="sections[about][heading_highlight]" value="{{ $t['about']['heading_highlight'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Personal Biography</label>
                                <textarea name="sections[about][biography]" rows="3" class="form-control">{{ $t['about']['biography'] ?? '' }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Stat 1 (Years Experience)</label>
                                <div class="input-group">
                                    <input type="text" name="sections[about][stat_1_number]" value="{{ $t['about']['stat_1_number'] ?? '3+' }}" class="form-control" style="max-width: 85px;">
                                    <input type="text" name="sections[about][stat_1_label]" value="{{ $t['about']['stat_1_label'] ?? 'Years Experience' }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Stat 2 (Projects Delivered)</label>
                                <div class="input-group">
                                    <input type="text" name="sections[about][stat_2_number]" value="{{ $t['about']['stat_2_number'] ?? '50+' }}" class="form-control" style="max-width: 85px;">
                                    <input type="text" name="sections[about][stat_2_label]" value="{{ $t['about']['stat_2_label'] ?? 'Projects Delivered' }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Stat 3 (Client Satisfaction)</label>
                                <div class="input-group">
                                    <input type="text" name="sections[about][stat_3_number]" value="{{ $t['about']['stat_3_number'] ?? '100%' }}" class="form-control" style="max-width: 85px;">
                                    <input type="text" name="sections[about][stat_3_label]" value="{{ $t['about']['stat_3_label'] ?? 'Client Satisfaction' }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($currentTab === 'process')
            <div class="sb-table-card p-4 mb-4">
                <h5 class="fw-bold mb-3">Why Work With Me (6 Core Value Cards)</h5>
                <div class="row g-3">
                    @foreach(($t['why_me']['items'] ?? []) as $wIdx => $wItem)
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <input type="hidden" name="sections[why_me][items][{{ $wIdx }}][icon]" value="{{ $wItem['icon'] }}">
                                <label class="form-label fw-bold small">Card #{{ $wIdx + 1 }} Title</label>
                                <input type="text" name="sections[why_me][items][{{ $wIdx }}][title]" value="{{ $wItem['title'] }}" class="form-control form-control-sm mb-2">
                                <label class="form-label small text-muted">Description</label>
                                <textarea name="sections[why_me][items][{{ $wIdx }}][desc]" rows="2" class="form-control form-control-sm">{{ $wItem['desc'] }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="sb-table-card p-4">
                <h5 class="fw-bold mb-3">My 7-Step Working Process</h5>
                <div class="row g-3">
                    @foreach(($t['process']['steps'] ?? []) as $sIdx => $step)
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <input type="hidden" name="sections[process][steps][{{ $sIdx }}][step]" value="{{ $step['step'] }}">
                                <label class="form-label fw-bold small">Step {{ $step['step'] }} Title</label>
                                <input type="text" name="sections[process][steps][{{ $sIdx }}][title]" value="{{ $step['title'] }}" class="form-control form-control-sm mb-2">
                                <textarea name="sections[process][steps][{{ $sIdx }}][desc]" rows="2" class="form-control form-control-sm">{{ $step['desc'] }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        @else
            <div class="row g-4">
                <!-- Left Column: Theme Mode, Brand, Contact, Socials, SEO -->
                <div class="col-lg-7">
                    <div class="sb-table-card p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-circle-half text-primary me-1"></i> Default Theme Mode &amp; Brand Settings</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Default Frontend Theme Mode</label>
                                <select name="sections[meta][default_theme]" class="form-select">
                                    <option value="dark" {{ ($t['meta']['default_theme'] ?? 'dark') === 'dark' ? 'selected' : '' }}>Dark &amp; Light Alternating Waves (Reference Default)</option>
                                    <option value="light" {{ ($t['meta']['default_theme'] ?? '') === 'light' ? 'selected' : '' }}>Full Light Mode (Daylight Clean)</option>
                                    <option value="full-dark" {{ ($t['meta']['default_theme'] ?? '') === 'full-dark' ? 'selected' : '' }}>Full Dark Navy Mode (All Sections Dark)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Developer Brand Name (Menu Name)</label>
                                <input type="text" name="sections[header][brand_name]" value="{{ $t['header']['brand_name'] ?? 'Alex Morgan' }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Header Brand Subtitle</label>
                                <input type="text" name="sections[header][brand_sub]" value="{{ $t['header']['brand_sub'] ?? 'Web Developer' }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Contact Email</label>
                                <input type="email" name="sections[contact][email]" value="{{ $t['contact']['email'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Contact Phone</label>
                                <input type="text" name="sections[contact][phone]" value="{{ $t['contact']['phone'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Location</label>
                                <input type="text" name="sections[contact][location]" value="{{ $t['contact']['location'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Footer Copyright</label>
                                <input type="text" name="sections[footer][copyright]" value="{{ $t['footer']['copyright'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">SEO Page Title</label>
                                <input type="text" name="sections[meta][page_title]" value="{{ $t['meta']['page_title'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">SEO Meta Description</label>
                                <textarea name="sections[meta][meta_description]" rows="2" class="form-control">{{ $t['meta']['meta_description'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Section Visibility Switches -->
                <div class="col-lg-5">
                    <div class="sb-table-card p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-eye text-primary me-1"></i> Section Visibility Control</h5>
                        <p class="text-muted small mb-3">Enable or disable any section on the public portfolio website.</p>
                        @php
                            $secLabels = [
                                'hero' => '01. Hero Banner Section',
                                'about' => '02. About Me Section',
                                'skills' => '03. Skills & Technologies',
                                'services' => '04. My Services',
                                'portfolio' => '05. Portfolio / Recent Works',
                                'experience' => '06. Career Timeline Experience',
                                'testimonials' => '07. Client Testimonials',
                                'blog' => '08. Latest Blog Articles',
                                'why_me' => '09. Why Work With Me',
                                'process' => '10. 7-Step Working Process',
                                'education' => '11. Education & Certifications',
                                'faq' => '12. FAQ Accordion',
                                'contact' => '13. Contact Section & Form',
                                'newsletter' => '14. Newsletter Subscription',
                            ];
                        @endphp
                        <div class="d-flex flex-column gap-2">
                            @foreach($secLabels as $sKey => $sLabel)
                                <div class="d-flex align-items-center justify-content-between p-2 rounded border bg-light">
                                    <span class="small fw-semibold">{{ $sLabel }}</span>
                                    <select name="sections[visibility][{{ $sKey }}]" class="form-select form-select-sm" style="width: 110px;">
                                        <option value="1" {{ ($t['visibility'][$sKey] ?? '1') === '1' ? 'selected' : '' }}>Visible</option>
                                        <option value="0" {{ ($t['visibility'][$sKey] ?? '1') === '0' ? 'selected' : '' }}>Hidden</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-4 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary fw-bold px-4 py-2 rounded-3">
                <i class="bi bi-check2-circle me-1"></i> Save Changes to port_settings
            </button>
        </div>
    </form>
@endsection
