<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use Database\Seeders\PortfolioSeeder;
use App\Services\PortfolioContent;
use Tests\TestCase;

class PortfolioPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        User::create(['firstName' => 'Sanish', 'lastName' => 'Maharjan', 'email' => 'owner@example.test', 'password' => bcrypt('test-password'), 'github' => config('portfolio.profile.github')]);
        $this->seed(PortfolioSeeder::class);
    }

    private function refreshContent(): void
    {
        $this->app->forgetScopedInstances();
    }

    public function test_public_portfolio_pages_render(): void
    {
        foreach (['experience', 'projects', 'education', 'skills'] as $page) {
            $this->get('/'.$page)->assertOk()->assertSee('Sanish Maharjan');
        }
    }

    public function test_controllers_supply_portfolio_data_without_view_composer_events(): void
    {
        $this->app['events']->forget('composing: fields.*');
        $this->app['events']->forget('composing: partials.portfolio-site-data');
        $this->app['events']->forget('composing: partials.portfolio-heading');

        foreach (['/', '/home', '/experience', '/projects', '/education', '/skills'] as $path) {
            $this->get($path)->assertOk()
                ->assertViewHas('portfolio', fn ($data) => $data['profile']['name'] === 'Sanish Maharjan')
                ->assertSee('portfolio-site-data', false);
        }
    }

    public function test_education_and_skills_metadata_identifies_the_portfolio(): void
    {
        foreach (['education', 'skills'] as $page) {
            $this->get('/'.$page)
                ->assertSee('rel="canonical"', false)
                ->assertDontSee('portfolio.smaranjitghose.codes')
                ->assertDontSee('free portfolio template');
        }
    }

    public function test_resume_download_asset_exists(): void
    {
        $path = public_path(config('portfolio.resume'));

        $this->assertFileExists($path);
        $this->assertSame('%PDF-', file_get_contents($path, false, null, 0, 5));
    }

    public function test_bundled_portrait_is_a_valid_image(): void
    {
        $path = public_path('images/Portrait/pp.jpg');

        $this->assertFileExists($path);
        $dimensions = getimagesize($path);
        $this->assertIsArray($dimensions);
        $this->assertSame('image/jpeg', $dimensions['mime']);
        $this->assertGreaterThan(0, $dimensions[0]);
        $this->assertGreaterThan(0, $dimensions[1]);
    }

    public function test_skills_are_server_rendered_with_php_and_laravel_first(): void
    {
        $response = $this->get('/skills')->assertOk();
        $groups = config('portfolio.skill_groups');

        $this->assertSame(['PHP', 'Laravel', 'Node.js'], array_slice($groups[0]['skills'], 0, 3));
        foreach ($groups as $group) {
            $response->assertSee($group['title']);
            foreach ($group['skills'] as $skill) {
                $response->assertSee('<li>'.e($skill).'</li>', false);
            }
        }
        $response->assertDontSee('js/new/techstack.js', false);
    }

    public function test_education_preserves_existing_dates_and_activities_alongside_resume_courses(): void
    {
        $response = $this->get('/education')->assertOk();

        foreach (config('portfolio.education') as $education) {
            $response->assertSee($education['institution'])
                ->assertSee($education['qualification']);
            foreach ($education['courses'] as $course) {
                $response->assertSee($course);
            }
        }
        $response->assertSee('2015-2020')
            ->assertSee('Campion Academy')
            ->assertSee('Ujjwal Shishu Niketan Academy')
            ->assertSee('Participated in Hult Prize')
            ->assertDontSee('height:90vh')
            ->assertDontSee('timeline-content')
            ->assertDontSee('css/new/education.css', false);
    }

    public function test_public_pages_use_database_content_not_config_arrays(): void
    {
        $education = Education::first();
        $education->update(['degree' => 'Updated qualification from canonical content']);
        $education->section->update(['display_period' => 'A verified study period']);
        Skill::first()->update(['name' => 'Updated primary skill']);
        Experience::first()->update(['companyName' => 'Updated company']);
        Project::first()->update(['name' => 'Updated project']);

        $this->get('/education')->assertOk()
            ->assertSee('Updated qualification from canonical content')
            ->assertSee('A verified study period');
        $this->get('/skills')->assertOk()->assertSee('Updated primary skill');
        $this->get('/experience')->assertOk()->assertSee('Updated company')
            ->assertDontSee('js/new/experience.js', false);
        $this->get('/projects')->assertOk()->assertSee('Updated project')
            ->assertDontSee('js/new/project.js', false);
    }

    public function test_homepage_summary_and_roles_use_canonical_content(): void
    {
        User::first()->update(['summery' => 'Canonical profile summary', 'roles' => ['Canonical backend role']]);

        $this->view('fields.home', ['user' => null])
            ->assertSee('<p class="profile-summary">Canonical profile summary</p>', false)
            ->assertSee('content="Canonical profile summary"', false)
            ->assertSee('data-words="Canonical backend role"', false)
            ->assertSee('images/Portrait/pp.jpg', false);
    }

    public function test_homepage_does_not_require_admin_skills_or_education_records(): void
    {
        $this->get('/')->assertOk()->assertSee('images/Portrait/pp.jpg', false);

        User::first()->update(['photo_path' => 'configured-portrait.jpg']);
        $this->refreshContent();

        $this->get('/')->assertOk()->assertSee('images/configured-portrait.jpg', false);
    }

    public function test_resume_and_github_navigation_data_are_shared_across_pages(): void
    {
        User::first()->update(['resume_path' => 'updated-resume.pdf', 'github' => 'https://github.com/example']);

        foreach (['education', 'skills', 'experience', 'projects'] as $page) {
            $html = $this->get('/'.$page)->assertOk()->getContent();
            $this->assertSame(1, preg_match('/<script type="application\/json" id="portfolio-site-data">(.*?)<\/script>/s', $html, $matches));
            $data = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);

            $this->assertSame('updated-resume.pdf', $data['resumeFilename']);
            $this->assertSame(asset('updated-resume.pdf'), $data['resume']);
            $this->assertSame('https://github.com/example', $data['github']);
        }
    }

    public function test_experience_and_project_images_exist(): void
    {
        $items = array_merge(
            config('portfolio.experience'),
            config('portfolio.projects'),
            config('portfolio.additional_projects')
        );

        foreach ($items as $item) {
            if (isset($item['image'])) {
                $path = public_path($item['image']);
                $this->assertFileExists($path);
                $this->assertIsArray(getimagesize($path), $item['image'].' must be a valid image');
            }

        }
    }

    public function test_seeder_is_repeatable_and_does_not_overwrite_admin_edits(): void
    {
        Experience::first()->update(['companyName' => 'Edited company']);
        Education::first()->update(['degree' => 'Edited qualification']);
        Skill::first()->update(['name' => 'Edited skill', 'isShown' => false]);
        Project::first()->update(['name' => 'Edited project']);
        $counts = [Experience::count(), Education::count(), Skill::count(), Project::count()];
        $this->seed(PortfolioSeeder::class);
        $this->assertSame($counts, [Experience::count(), Education::count(), Skill::count(), Project::count()]);
        $this->assertSame('Edited company', Experience::first()->companyName);
        $this->assertSame('Edited qualification', Education::first()->degree);
        $this->assertSame('Edited skill', Skill::first()->name);
        $this->assertFalse((bool) Skill::first()->isShown);
        $this->assertSame('Edited project', Project::first()->name);
    }

    public function test_hidden_items_do_not_reappear_from_config(): void
    {
        Experience::first()->section->update(['isShown' => false]);
        Education::first()->section->update(['isShown' => false]);
        Skill::where('name', 'PHP')->update(['isShown' => false]);
        Project::first()->update(['isShown' => false]);
        $this->get('/experience')->assertOk()->assertDontSee('Aware Ads Inc.');
        $this->get('/education')->assertOk()->assertDontSee('Tribhuvan University');
        $this->get('/skills')->assertOk()->assertDontSee('<li>PHP</li>', false);
        $this->get('/projects')->assertOk()->assertDontSee('RBC PayEdge &amp; QuickPay APIs', false);
    }

    public function test_cleared_profile_fields_do_not_reappear_from_seed_config(): void
    {
        User::first()->update(['github' => null, 'summery' => null]);
        $portfolio = app(PortfolioContent::class)->all();
        $this->assertNull($portfolio['profile']['github']);
        $this->assertSame('', $portfolio['profile']['summary']);
    }
}
