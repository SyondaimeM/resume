<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Throwable;

class PortfolioProfileController extends Controller
{
    public function edit(Request $request)
    {
        abort_unless((int) User::min('id') === (int) $request->user()->id, 403);

        return view('admin.content.profile', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        abort_unless((int) User::min('id') === (int) $request->user()->id, 403);
        $data = $request->validate([
            'firstName' => 'required|string|max:30',
            'lastName' => 'required|string|max:30',
            'summery' => 'required|string|max:10000',
            'roles' => 'required|string|max:2000',
            'github' => 'nullable|url:http,https|max:255',
            'linkedin' => 'nullable|url:http,https|max:255',
            'instagram' => 'nullable|url:http,https|max:255',
            'resume' => 'nullable|file|mimes:pdf|max:10240',
        ]);
        $data['roles'] = array_values(array_filter(array_map('trim', preg_split('/\R/', $data['roles'])), fn ($role) => $role !== ''));
        unset($data['resume']);
        if ($request->hasFile('resume')) {
            File::ensureDirectoryExists(public_path('resumes'));
            $data['resume_path'] = 'resumes/'.$request->file('resume')->hashName();
            $request->file('resume')->move(public_path('resumes'), basename($data['resume_path']));
        }
        try {
            $request->user()->fill($data)->save();
        } catch (Throwable $exception) {
            if (isset($data['resume_path'])) {
                File::delete(public_path($data['resume_path']));
            }
            throw $exception;
        }

        return back()->with('success', 'Public profile and resume download updated.');
    }
}
