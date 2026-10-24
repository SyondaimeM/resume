<?php

namespace Tests\Feature;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PortfolioAdminTest extends TestCase
{
    private User $owner;
    private array $uploads = [];

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->owner = User::create(['firstName' => 'Sanish', 'lastName' => 'Maharjan', 'email' => 'owner@example.test', 'password' => bcrypt('test-password')]);
        $this->seed(PortfolioSeeder::class);
    }

    protected function tearDown(): void
    {
        foreach ($this->uploads as $path) {
            File::delete($path);
        }
        parent::tearDown();
    }

    public function test_admin_lists_and_forms_render_and_guests_are_denied(): void
    {
        $this->get('/admin/experience')->assertRedirect('/login');
        $this->actingAs($this->owner);
        foreach (['experience', 'education', 'skill', 'project'] as $kind) {
            $this->get('/admin/'.$kind)->assertOk();
            $this->get('/admin/'.$kind.'/create')->assertOk();
        }
        $this->get('/admin/portfolio')->assertOk();
    }

    public function test_adding_editing_hiding_and_deleting_experience_updates_public_page(): void
    {
        $this->actingAs($this->owner);
        $data = ['title' => 'Senior PHP Engineer with a title longer than thirty characters', 'companyName' => 'New Company', 'details' => "First contribution\nSecond contribution", 'country' => 'Canada', 'city' => 'Toronto', 'startDate' => '2026-01-01', 'endDate' => null, 'isActive' => 1, 'isShown' => 1, 'sort_order' => 0];
        $this->post('/admin/experience/store', $data)->assertSessionHasNoErrors()->assertRedirect('/admin/experience');
        $item = Experience::where('companyName', 'New Company')->firstOrFail();
        $this->get('/experience')->assertOk()->assertSee('New Company')->assertSee('First contribution')->assertSee('January 2026 - Present');
        $data['companyName'] = 'Updated Company';
        $data['isShown'] = 0;
        $this->post('/admin/experience/update/'.$item->id, $data)->assertSessionHasNoErrors();
        $this->app->forgetScopedInstances();
        $this->get('/experience')->assertOk()->assertDontSee('Updated Company')->assertDontSee('New Company');
        $this->get('/admin/experience/edit/'.$item->id)->assertOk()->assertSee('Updated Company');
        $sectionId = $item->section_id;
        $this->delete('/admin/experience/delete/'.$item->id)->assertRedirect('/admin/experience');
        $this->assertDatabaseMissing('sections', ['id' => $sectionId]);
        $this->assertDatabaseMissing('experiences', ['id' => $item->id]);
    }

    public function test_education_skills_and_projects_are_editable_and_visible(): void
    {
        $this->actingAs($this->owner);
        $this->post('/admin/education/store', ['title' => 'Continuing education', 'collageName' => 'New Institution', 'degree' => 'New Qualification', 'startDate' => '2026-01-01', 'endDate' => '2026-03-01', 'isActive' => 0, 'isShown' => 1, 'sort_order' => 5, 'courses' => "New course\nSecond course", 'achievements' => 'New activity'])->assertSessionHasNoErrors();
        $this->post('/admin/skill/store', ['name' => 'New skill', 'group_name' => 'New group', 'group_order' => 0, 'sort_order' => 0, 'isShown' => 1])->assertSessionHasNoErrors();
        $this->post('/admin/project/store', ['name' => 'New project', 'body' => str_repeat('Project details ', 40), 'link' => 'https://example.test/project', 'technologies' => "PHP\nLaravel", 'featured' => 1, 'isShown' => 1, 'sort_order' => 0])->assertSessionHasNoErrors();
        $this->get('/education')->assertOk()->assertSee('New Institution')->assertSee('New course')->assertSee('New activity');
        $this->get('/skills')->assertOk()->assertSee('New group')->assertSee('New skill');
        $this->get('/projects')->assertOk()->assertSee('New project')->assertSee(trim(str_repeat('Project details ', 40)));
        foreach (['education' => Education::class, 'skill' => Skill::class, 'project' => Project::class] as $kind => $model) {
            $this->get('/admin/'.$kind.'/edit/'.$model::latest('id')->first()->id)->assertOk();
        }
    }

    public function test_invalid_input_and_other_accounts_cannot_edit_public_content(): void
    {
        $this->actingAs($this->owner)->post('/admin/project/store', ['name' => 'bad', 'link' => 'javascript:alert(1)'])->assertSessionHasErrors(['body', 'link']);
        $other = User::create(['firstName' => 'Other', 'lastName' => 'User', 'email' => 'other@example.test', 'password' => bcrypt('test-password')]);
        $this->actingAs($other)->get('/admin/experience')->assertForbidden();
        $this->post('/admin/experience/update/'.Experience::first()->id, [])->assertForbidden();
        $this->post('/admin/portfolio', [])->assertForbidden();
    }

    public function test_invalid_resume_and_image_paths_are_rejected_without_changing_data(): void
    {
        $this->actingAs($this->owner);
        $this->post('/admin/portfolio', ['firstName' => 'Sanish', 'lastName' => 'Maharjan', 'summery' => 'Summary', 'roles' => 'PHP Engineer', 'resume' => UploadedFile::fake()->create('not-a-resume.txt', 1, 'text/plain')])
            ->assertSessionHasErrors('resume');
        $this->post('/admin/project/store', ['name' => 'Invalid image', 'body' => 'Description', 'image' => 'images/does-not-exist.png', 'featured' => 1, 'isShown' => 1, 'sort_order' => 0])
            ->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('projects', ['name' => 'Invalid image']);
        $this->assertSame(config('portfolio.resume'), $this->owner->fresh()->resume_path);
    }

    public function test_uploaded_resume_and_profile_update_all_public_pages(): void
    {
        $pdf = UploadedFile::fake()->createWithContent('resume.pdf', file_get_contents(public_path(config('portfolio.resume'))));
        $this->actingAs($this->owner)->post('/admin/portfolio', ['firstName' => 'Sanish', 'lastName' => 'Maharjan', 'summery' => 'New public summary', 'roles' => "PHP Engineer\nBackend Engineer", 'github' => 'https://github.com/example', 'resume' => $pdf])->assertSessionHasNoErrors();
        $owner = $this->owner->fresh();
        $this->uploads[] = public_path($owner->resume_path);
        $this->assertFileExists(public_path($owner->resume_path));
        $this->get('/')->assertOk()->assertSee('New public summary')->assertSee('PHP Engineer');
        $this->get('/education')->assertOk()->assertSee(basename($owner->resume_path));
        $this->assertSame(['PHP Engineer', 'Backend Engineer'], $owner->roles);
    }
}
