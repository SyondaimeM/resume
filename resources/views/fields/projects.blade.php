@extends('layouts.portfolio')
@section('title', 'Projects')
@section('description', "Sanish Maharjan's backend, financial services, real-time data, and full-stack projects.")

@section('content')
    @include('partials.portfolio-heading', [
        'section' => 'Projects',
        'heading' => 'Selected projects',
        'description' => 'Backend, financial services, real-time data, and full-stack work, including the projects featured in my resume.',
    ])
    <div class="portfolio-projects">
        @foreach($portfolio['projects'] as $project)
            @include('partials.portfolio-project', ['project' => $project])
        @endforeach
    </div>
    <section class="additional-projects" aria-labelledby="additional-projects-title">
        <h2 id="additional-projects-title">More portfolio projects</h2>
        <p>Additional work from the existing portfolio; not listed in the supplied 2026 resume.</p>
        <div class="portfolio-projects">
            @foreach($portfolio['additional_projects'] as $project)
                @include('partials.portfolio-project', ['project' => $project])
            @endforeach
        </div>
    </section>
@endsection
