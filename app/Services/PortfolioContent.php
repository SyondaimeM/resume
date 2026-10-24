<?php

namespace App\Services;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Section;
use App\Models\User;
use Carbon\Carbon;

class PortfolioContent
{
    private ?array $content = null;

    public function all(): array
    {
        if ($this->content !== null) {
            return $this->content;
        }
        $owner = User::orderBy('id')->first();
        $ownerId = $owner?->id;
        $sections = fn ($query) => $query->where('user_id', $ownerId)->where('isShown', true);
        $education = Education::with('section')->whereHas('section', $sections)->get()
            ->sortBy(fn ($item) => [$item->section->sort_order, $item->id])
            ->map(fn ($item) => [
                'institution' => $item->collageName,
                'qualification' => $item->degree,
                'period' => $this->period($item->section),
                'details' => $this->lines($item->section->details),
                'courses' => $item->courses ?? [],
                'achievements' => $item->achievements ?? [],
                'image' => $item->image,
            ])->values()->all();
        $experience = Experience::with('section')->whereHas('section', $sections)->get()
            ->sortBy(fn ($item) => [$item->section->sort_order, $item->id])
            ->map(fn ($item) => [
                'company' => $item->companyName, 'title' => $item->section->title,
                'location' => implode(', ', array_filter([$item->section->city, $item->section->country])),
                'period' => $this->period($item->section), 'image' => $item->image, 'brand' => $item->brand,
                'responsibilities' => $this->lines($item->section->details),
            ])->values()->all();
        $skills = Skill::where('user_id', $ownerId)->where('isShown', true)
            ->orderBy('group_order')->orderBy('sort_order')->orderBy('id')->get()
            ->groupBy('group_name')->map(fn ($items, $name) => [
                'title' => $name, 'description' => $items->first()->group_description,
                'skills' => $items->pluck('name')->all(),
                'descriptions' => $items->pluck('description', 'name')->all(),
            ])->values()->all();
        $projects = Project::where(function ($query) use ($ownerId) {
            $query->where('user_id', $ownerId)->orWhereNull('user_id');
        })->where('isShown', true)->orderBy('sort_order')->orderBy('id')->get();
        $mapProject = fn ($item) => [
            'title' => $item->name, 'description' => $item->body ?? $item->description,
            'url' => $item->link, 'link_label' => $item->link_label,
            'image' => $item->image, 'brand' => $item->brand ?: $item->name,
            'technologies' => $item->technologies ?? [], 'context' => $item->context,
        ];

        return $this->content = [
            'profile' => [
                'name' => $owner ? trim($owner->firstName.' '.$owner->lastName) : config('portfolio.profile.name'),
                'summary' => $owner ? ($owner->summery ?? '') : config('portfolio.profile.summary'),
                'roles' => $owner?->roles ?? config('portfolio.profile.roles'),
                'github' => $owner ? $owner->github : config('portfolio.profile.github'),
                'linkedin' => $owner?->linkedin,
                'instagram' => $owner?->instagram,
            ],
            'resume' => $owner?->resume_path ?? config('portfolio.resume'),
            'education' => $education, 'experience' => $experience, 'skill_groups' => $skills,
            'projects' => $projects->where('featured', true)->map($mapProject)->values()->all(),
            'additional_projects' => $projects->where('featured', false)->map($mapProject)->values()->all(),
        ];
    }

    public function lines(?string $value): array
    {
        $text = preg_replace('/<\/(?:p|li|div)>|<br\s*\/?>/i', "\n", $value ?? '');

        return array_values(array_filter(array_map(
            'trim',
            preg_split('/\R/', html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5))
        ), fn ($line) => $line !== ''));
    }

    private function period(Section $section): string
    {
        if ($section->display_period) {
            return $section->display_period;
        }

        return Carbon::parse($section->startDate)->format('F Y').' - '.
            ($section->isActive ? 'Present' : ($section->endDate ? Carbon::parse($section->endDate)->format('F Y') : ''));
    }
}
