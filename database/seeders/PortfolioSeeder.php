<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Section;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PortfolioSeeder extends Seeder
{
    public function run()
    {
        $owner = User::orderBy('id')->first();
        if (!$owner) {
            throw new RuntimeException('Create the portfolio owner account before running PortfolioSeeder.');
        }

        DB::transaction(function () use ($owner) {
            foreach (['education' => Education::class, 'experience' => Experience::class] as $kind => $model) {
                foreach (config('portfolio.'.$kind) as $index => $item) {
                    $nameField = $kind === 'experience' ? 'companyName' : 'collageName';
                    $name = $item[$kind === 'experience' ? 'company' : 'institution'];
                    $key = Str::slug($name);
                    $record = $model::where('seed_key', $key)->first()
                        ?? $model::where($nameField, $name)->whereHas('section', fn ($q) => $q->where('user_id', $owner->id))->first();
                    if ($record) {
                        if (!$record->seed_key) {
                            $record->update(['seed_key' => $key]);
                        }
                        continue;
                    }
                    $section = Section::create([
                        'user_id' => $owner->id,
                        'title' => $item[$kind === 'experience' ? 'title' : 'qualification'],
                        'details' => $kind === 'experience' ? implode("\n", $item['responsibilities']) : '',
                        'country' => '', 'city' => $item['location'] ?? '',
                        'startDate' => $item['start'], 'endDate' => $item['end'],
                        'display_period' => $item['period'], 'sort_order' => $index,
                        'isActive' => $item['end'] === null, 'isShown' => true,
                    ]);
                    $values = [$nameField => $name, 'section_id' => $section->id, 'seed_key' => $key, 'image' => $item['image'] ?? null];
                    if ($kind === 'experience') {
                        $values['brand'] = $item['brand'] ?? null;
                    } else {
                        $values += ['degree' => $item['qualification'], 'department' => '', 'gpa' => 0, 'courses' => $item['courses'], 'achievements' => $item['achievements']];
                    }
                    $model::create($values);
                }
            }
            foreach (config('portfolio.skill_groups') as $groupOrder => $group) {
                foreach ($group['skills'] as $order => $name) {
                    $key = Str::slug($name).'-'.substr(sha1($name), 0, 8);
                    $skill = Skill::where('seed_key', $key)->first()
                        ?? Skill::where('user_id', $owner->id)->where('name', $name)->first();
                    if ($skill) {
                        if (!$skill->seed_key) {
                            $skill->update(['seed_key' => $key, 'group_name' => $group['title'], 'group_description' => $group['description'], 'group_order' => $groupOrder, 'sort_order' => $order]);
                        }
                        continue;
                    }
                    Skill::create(['seed_key' => $key, 'user_id' => $owner->id, 'name' => $name, 'isShown' => true, 'group_name' => $group['title'], 'group_description' => $group['description'], 'group_order' => $groupOrder, 'sort_order' => $order]);
                }
            }
            foreach ([true => 'projects', false => 'additional_projects'] as $featured => $source) {
                foreach (config('portfolio.'.$source) as $index => $item) {
                    $key = Str::slug($item['title']);
                    $project = Project::where('seed_key', $key)->first()
                        ?? Project::where('name', $item['title'])->where(function ($q) use ($owner) {
                            $q->where('user_id', $owner->id)->orWhereNull('user_id');
                        })->first();
                    if ($project) {
                        if (!$project->seed_key) {
                            $project->update(['seed_key' => $key, 'user_id' => $owner->id]);
                        }
                        continue;
                    }
                    Project::create([
                        'seed_key' => $key, 'user_id' => $owner->id, 'name' => $item['title'],
                        'description' => Str::limit($item['description'], 250, ''),
                        'body' => $item['description'], 'link' => $item['url'] ?? '',
                        'image' => $item['image'] ?? null, 'brand' => $item['brand'] ?? null,
                        'technologies' => $item['technologies'] ?? [],
                        'context' => $item['context'] ?? null, 'link_label' => $item['link_label'] ?? null,
                        'featured' => $featured, 'isShown' => true, 'sort_order' => $index,
                    ]);
                }
            }
            $owner->roles ??= config('portfolio.profile.roles');
            $owner->resume_path ??= config('portfolio.resume');
            if (!$owner->summery) {
                $owner->summery = config('portfolio.profile.summary');
            }
            $owner->save();
        });
    }
}
