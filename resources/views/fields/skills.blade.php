@extends('layouts.portfolio')
@section('title', 'Skills')
@section('description', "Explore Sanish Maharjan's PHP, Laravel, Node.js, database, cloud, and full-stack development skills.")

@section('content')
    @include('partials.portfolio-heading', [
        'section' => 'Skills',
        'heading' => 'Technical skills',
        'description' => 'PHP and Laravel first, followed by backend, data, cloud, and frontend technologies. Ordered by relevance to my work, not proficiency ratings.',
    ])
    <div class="skills-groups">
        @foreach($portfolio['skill_groups'] as $group)
            <section class="portfolio-card skill-group" aria-labelledby="skill-group-{{ $loop->index }}">
                <h2 id="skill-group-{{ $loop->index }}">{{ $group['title'] }}</h2>
                <p>{{ $group['description'] }}</p>
                <ul class="skill-list">
                    @foreach($group['skills'] as $skill)
                        <li>{{ $skill }}</li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
@endsection
