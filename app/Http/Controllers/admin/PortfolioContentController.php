<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Section;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class PortfolioContentController extends Controller
{
    private const MODELS = ['experience' => Experience::class, 'education' => Education::class, 'skill' => Skill::class, 'project' => Project::class];

    private function kind(Request $request): string
    {
        abort_unless((int) User::min('id') === (int) $request->user()->id, 403);
        $kind = $request->route('kind');
        abort_unless(isset(self::MODELS[$kind]), 404);

        return $kind;
    }

    private function query(string $kind, int $owner)
    {
        $query = self::MODELS[$kind]::query();
        if (in_array($kind, ['education', 'experience'], true)) {
            return $query->with('section')->whereHas('section', fn ($q) => $q->where('user_id', $owner));
        }

        return $query->where(function ($q) use ($kind, $owner) {
            $q->where('user_id', $owner);
            if ($kind === 'project') {
                $q->orWhereNull('user_id');
            }
        });
    }

    public function index(Request $request)
    {
        $kind = $this->kind($request);
        $items = $this->query($kind, $request->user()->id)->orderBy('id')->paginate(30);

        return view('admin.content.index', compact('kind', 'items'));
    }

    public function create(Request $request)
    {
        $kind = $this->kind($request);

        return view('admin.content.form', ['kind' => $kind, 'values' => [], 'id' => null]);
    }

    public function edit(Request $request, int $id)
    {
        $kind = $this->kind($request);
        $item = $this->query($kind, $request->user()->id)->findOrFail($id);
        $values = $item->toArray();
        if ($item->relationLoaded('section')) {
            $values = array_merge($values, $item->section->toArray());
        }
        foreach (['courses', 'achievements', 'technologies'] as $field) {
            if (isset($values[$field])) {
                $values[$field] = implode("\n", $values[$field]);
            }
        }
        if ($kind === 'project') {
            $values['body'] = $item->body ?? $item->description;
        }

        return view('admin.content.form', compact('kind', 'values', 'id'));
    }

    public function store(Request $request)
    {
        return $this->save($request);
    }

    public function update(Request $request, int $id)
    {
        return $this->save($request, $id);
    }

    private function save(Request $request, ?int $id = null)
    {
        $kind = $this->kind($request);
        $sectional = in_array($kind, ['education', 'experience'], true);
        $rules = ['isShown' => 'required|boolean', 'sort_order' => 'required|integer|min:0|max:100000'];
        if ($sectional) {
            $rules += [
                'title' => 'required|string|max:255', 'details' => 'nullable|string|max:20000',
                'country' => 'nullable|string|max:255', 'city' => 'nullable|string|max:255',
                'startDate' => 'required|date', 'endDate' => 'nullable|date|after_or_equal:startDate',
                'isActive' => 'required|boolean', 'display_period' => 'nullable|string|max:255',
            ];
        }
        $imageRule = ['nullable', 'string', 'max:255', 'regex:~^images/[a-zA-Z0-9/_ .-]+\.(png|jpg|jpeg|gif|webp)$~i', 'not_regex:~\.\.~'];
        $imageRule[] = function ($attribute, $value, $fail) {
            if (!is_file(public_path($value))) {
                $fail('Choose an existing image below public/images.');
            }
        };
        if ($kind === 'experience') {
            $rules += ['companyName' => 'required|string|max:255', 'brand' => 'nullable|string|max:255', 'image' => $imageRule];
        } elseif ($kind === 'education') {
            $rules += ['collageName' => 'required|string|max:255', 'degree' => 'required|string|max:255', 'department' => 'nullable|string|max:255', 'gpa' => 'nullable|numeric|min:0|max:100', 'courses' => 'nullable|string|max:10000', 'achievements' => 'nullable|string|max:10000', 'image' => $imageRule];
        } elseif ($kind === 'skill') {
            $rules += ['name' => 'required|string|max:255', 'description' => 'nullable|string|max:255', 'group_name' => 'required|string|max:255', 'group_description' => 'nullable|string|max:255', 'group_order' => 'required|integer|min:0|max:100000'];
        } else {
            $rules += ['name' => 'required|string|max:255', 'body' => 'required|string|max:20000', 'link' => 'nullable|url:http,https|max:255', 'link_label' => 'nullable|string|max:255', 'context' => 'nullable|string|max:10000', 'technologies' => 'nullable|string|max:10000', 'image' => $imageRule, 'brand' => 'nullable|string|max:255', 'featured' => 'required|boolean'];
        }
        if ($kind !== 'skill') {
            $rules['image_upload'] = 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120';
        }
        $data = $request->validate($rules);
        $item = $id ? $this->query($kind, $request->user()->id)->findOrFail($id) : new (self::MODELS[$kind]);
        unset($data['image_upload']);
        $uploadedPath = null;
        if ($kind !== 'skill' && $request->hasFile('image_upload')) {
            File::ensureDirectoryExists(public_path('images/uploads'));
            $uploadedPath = 'images/uploads/'.$request->file('image_upload')->hashName();
            $request->file('image_upload')->move(public_path('images/uploads'), basename($uploadedPath));
            $data['image'] = $uploadedPath;
        }
        try {
            $this->persist($data, $item, $kind, $sectional, $request);
        } catch (Throwable $exception) {
            if ($uploadedPath) {
                File::delete(public_path($uploadedPath));
            }
            throw $exception;
        }

        return redirect('/admin/'.$kind)->with('success', 'Saved. The public portfolio now uses this data.');
    }

    private function persist(array $data, $item, string $kind, bool $sectional, Request $request): void
    {
        DB::transaction(function () use ($data, $item, $kind, $sectional, $request) {
            $values = $data;
            foreach (['courses', 'achievements', 'technologies'] as $field) {
                if (array_key_exists($field, $values)) {
                    $values[$field] = array_values(array_filter(array_map('trim', preg_split('/\R/', $values[$field] ?? '')), fn ($line) => $line !== ''));
                }
            }
            if ($sectional) {
                $fields = ['title', 'details', 'country', 'city', 'startDate', 'endDate', 'isActive', 'isShown', 'display_period', 'sort_order'];
                $sectionData = array_intersect_key($values, array_flip($fields));
                foreach (['details', 'country', 'city'] as $field) {
                    $sectionData[$field] = $sectionData[$field] ?? '';
                }
                $sectionData['details'] = strip_tags($sectionData['details']);
                if ($sectionData['isActive']) {
                    $sectionData['endDate'] = null;
                }
                $section = $item->exists ? $item->section : new Section(['user_id' => $request->user()->id]);
                if ($section->exists && ($sectionData['display_period'] ?? null) === $section->display_period) {
                    foreach (['startDate', 'endDate', 'isActive'] as $field) {
                        if ((string) ($sectionData[$field] ?? '') !== (string) $section->$field) {
                            $sectionData['display_period'] = null;
                            break;
                        }
                    }
                }
                $section->fill($sectionData)->save();
                $values = array_diff_key($values, array_flip($fields));
                $values['section_id'] = $section->id;
            } else {
                $values['user_id'] = $request->user()->id;
            }
            if ($kind === 'education') {
                $values['department'] = $values['department'] ?? '';
                $values['gpa'] = $values['gpa'] ?? 0;
            } elseif ($kind === 'project') {
                $values['description'] = Str::limit($values['body'], 250, '');
                $values['link'] = $values['link'] ?? '';
            }
            $item->fill($values)->save();
            if ($kind === 'skill') {
                Skill::where('user_id', $request->user()->id)->where('group_name', $item->group_name)->update([
                    'group_description' => $item->group_description,
                    'group_order' => $item->group_order,
                ]);
            }
        });
    }

    public function destroy(Request $request, int $id)
    {
        $kind = $this->kind($request);
        $item = $this->query($kind, $request->user()->id)->findOrFail($id);
        DB::transaction(function () use ($item) {
            if ($item instanceof Experience || $item instanceof Education) {
                $item->section->delete();
            } else {
                $item->delete();
            }
        });

        return redirect('/admin/'.$kind)->with('success', 'Deleted. Hide records instead if you want to retain them.');
    }
}
