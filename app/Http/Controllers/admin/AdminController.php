<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.index');
    }

    public function profile(Request $request)
    {
        return view('admin.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request, int $id)
    {
        abort_unless($id === (int) $request->user()->id, 403);
        $data = $request->validate([
            'firstName' => 'required|string|max:30',
            'lastName' => 'required|string|max:30',
            'phone_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'summery' => 'nullable|string|max:10000',
            'linkedin' => 'nullable|url:http,https|max:255',
            'instagram' => 'nullable|url:http,https|max:255',
            'github' => 'nullable|url:http,https|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,bmp|max:10240',
        ]);
        unset($data['photo']);
        $path = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('Portrait', 'images');
            if (!$path) {
                throw new RuntimeException('The profile image could not be stored.');
            }
            $data['photo_path'] = $path;
        }
        try {
            $request->user()->fill($data)->save();
        } catch (Throwable $exception) {
            if ($path) {
                Storage::disk('images')->delete($path);
            }
            throw $exception;
        }

        return redirect('/admin/profile')->with('success', 'Profile saved. Public profile fields are now synchronized.');
    }
}
