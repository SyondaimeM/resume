<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SkillController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $skills= Skill::all();
        return view('admin.skill.index', compact('skills'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:30',
        ]);
        try {
            // Safely perform set of DB related queries if fail rollback all.
            DB::transaction(function () use ($request){
                Skill::create($request->all());
            });
        } catch (\Exception $exception){
            // Back to form with errors
            return redirect('/admin/skill')
                ->withErrors($exception->getMessage());
        }
        return redirect('/admin/skill')-> with('success', 'A new skill had been added Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function show(Skill $skill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function edit(Skill $skill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, int $id)
    {
        $this->validate($request, [
            'name' => 'required|max:30',
        ]);
        try {
            // Safely perform set of DB related queries if fail rollback all.
            DB::transaction(function () use ($request, $id){

                $skill = Skill::findOrFail($id);
                $skill->name = $request->name;
                $skill->description = $request->description;
                $skill->isShown = $request->isShown;
                $skill->save();
            });
        } catch (\Exception $exception){
            // Back to form with errors
            return redirect('/admin/skill')
                ->withErrors($exception->getMessage());
        }
        return redirect('/admin/skill')->with('success', 'A new skill had been added Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(int $id)
    {
        try {
            $skill = Skill::findOrFail($id);
            $skill->delete();
        } catch (\Exception $exception){
            echo $exception->getMessage();
        }
        return redirect('/admin/skill')->with('success', 'A section has been deleted Successfully.');
    }
}
