<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Section;
use App\Models\User;
use App\Models\Message;
use App\Services\ContactCaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = User::with('activeSkills')->first();
        $experiences = Experience::with('section')->whereHas('section', function ($e) use ($user) {
            return $e->where([['user_id',  $user->id],['isShown', 1]]);
        })->orderByDesc('created_at')->get();
        $education = Education::with('section')->whereHas('section', function ($e) use ($user) {
            return $e->where([['user_id',  $user->id],['isShown', 1]]);
        })->orderByDesc('created_at')->get();
        return view('fields.home', compact('user', 'experiences', 'education'));
    }

    public function experience()
    {
        return view('fields.experience');
    }

    public function education()
    {
        return view('fields.education');
    }

    public function projects()
    {
        return view('fields.projects');
    }

    public function skills()
    {
        return view('fields.skills');
    }

    public function captcha(Request $request, ContactCaptcha $captcha)
    {
        return response($captcha->image($request), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, private, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function message(Request $request, ContactCaptcha $captcha)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'captcha' => ['required', 'string', 'size:5'],
        ]);

        if (!$captcha->verify($request, $data['captcha'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'captcha' => 'The verification code is incorrect or expired. Enter the new code below.',
            ]);
        }

        try {
            Message::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'description' => $data['message'],
                'status' => 1,
            ]);
        } catch (QueryException $exception) {
            report($exception);

            return response()->json([
                'status' => 'error',
                'msg' => 'Your message could not be saved. Please try again later.',
            ], 503);
        }

        return response()->json([
            'status' => 'success',
            'msg' => 'Thank you! Your message has been received.',
        ], 201);
    }
}
