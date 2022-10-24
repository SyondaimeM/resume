<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
