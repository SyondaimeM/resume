<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExperienceController extends Controller
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
        $expSections = Experience::with('section')->paginate(10);
        return view('admin.experience.index', compact('expSections'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('admin.experience.view');
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
            'title' => 'required|max:30',
            'details' => 'required',
            'country' => 'required',
            //'city' => ['required',Rule::exists('classrooms', 'id')],
            'startDate' => 'required|date',
            'endDate' => 'after:startDate',
            'companyName' => 'required|max:100',
        ]);
        try {
            // Safely perform set of DB related queries if fail rollback all.
            DB::transaction(function () use ($request){
                $section = new Section();
                $this->sectionInfo($request, $section);
                $section->user_id = Auth::user()->id;
                $section->save();

                $experience = new Experience();
                $experience->companyName = $request->companyName;
                $experience->section_id = $section->id;
                $experience->save();

            });
        } catch (\Exception $exception){
            // Back to form with errors
            return redirect('/admin/experience/create')
                ->withInput()
                ->withErrors($exception->getMessage());
        }
        return redirect('/admin/experience')-> with('success', 'A new experience section had been added Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Experience  $experience
     * @return \Illuminate\Http\Response
     */
    public function show(Experience $experience)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(int $id)
    {
        $experience = Experience::with('section')->findOrFail($id);
        return view('admin.experience.view', compact('experience'));
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
            'title' => 'required|max:30',
            'details' => 'required',
            'country' => 'required',
            //'city' => ['required',Rule::exists('classrooms', 'id')],
            'startDate' => 'required|date',
            'endDate' => 'after:startDate|before:today',
            'companyName' => 'required|max:150',
        ]);
        try {
            DB::transaction(function () use ($request, $id){
                $experience = Experience::findOrFail($id);
                $section = Section::findOrFail($experience->section_id);
                $this->sectionInfo($request, $section);
                $section->save();

                $experience->companyName = $request->companyName;
                $experience->save();

            });

            return redirect('/admin/experience')->with('success', 'Experience section had been edited Successfully.');
        } catch (\Exception $exception){
            // Back to form with errors
            dd($exception->getMessage());
            return redirect('/admin/experience/edit')
                ->withErrors($exception->getMessage());
        }
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
            $experience = Experience::with('section')->find($id);
            $experience->section()->delete();
            $experience->delete();
        } catch (\Exception $exception){
            echo $exception->getMessage();
        }
        return redirect('/admin/experience')->with('success', 'A section has been deleted Successfully.');
    }

    /**
     * @param Request $request
     * @param $section
     * @return void
     */
    public function sectionInfo(Request $request, $section): void
    {
        $section->title = $request->title;
        $section->details = $request->details;
        $section->country = $request->country;
        $section->city = $request->city;
        $section->startDate = $request->startDate;
        $section->endDate = $request->endDate;
        $section->isActive = isset($request->isActive) ? 1 : 0;
        $section->isShown = isset($request->isShown) ? 1 : 0;
    }
}
