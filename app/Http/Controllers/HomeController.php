<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Section;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function message(Request $request)
    {   
        // dd($data);
        try {
            // Safely perform set of DB related queries if fail rollback all.
            DB::transaction(function () use ($request){
                $data = $request->all();
                $message = new Message();
                $message->name = $data['name'];
                $message->email = $data['email'];
                $message->description = $data['message'];
                $message->status = 1;
                $message->save();
            });
        } catch (\Exception $exception){
            // Back to form with errors
            return response()->json(['status'=>'401','msg'=>$exception->getMessage()]);
        }
        return response()->json(['status'=>'500','msg'=>'success']);
        // return redirect('/')-> with('success', 'Sent');

    }
}
