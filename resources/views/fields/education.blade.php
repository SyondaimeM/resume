@extends('layouts.portfolio')
@section('title', 'Education')
@section('description', "Explore Sanish Maharjan's educational background and foundation in software engineering.")

@section('content')
    @include('partials.portfolio-heading', [
        'section' => 'Education',
        'heading' => 'Education',
        'description' => 'Computer engineering fundamentals supporting practical backend and distributed systems work.',
    ])
    <section class="education-list" aria-label="Formal education">
        @foreach($portfolio['education'] as $education)
            <article class="portfolio-card education-card">
                @if($education['image'])
                    <img class="education-image" src="{{ asset($education['image']) }}" alt="" width="160" height="100" loading="lazy">
                @endif
                <h2>{{ $education['institution'] }}</h2>
                @isset($education['period'])
                    <p class="education-period">{{ $education['period'] }}</p>
                @endisset
                <p class="education-qualification">{{ $education['qualification'] }}</p>
                @if($education['courses'])
                <h3 class="h6">Key coursework</h3>
                <ul class="education-courses">
                    @foreach($education['courses'] as $course)
                        <li>{{ $course }}</li>
                    @endforeach
                </ul>
                @endif
                @if($education['achievements'] || $education['details'])
                    <h3 class="h6">Activities and achievements</h3>
                    <ul>
                        @foreach(array_merge($education['details'], $education['achievements']) as $detail)
                            <li>{{ $detail }}</li>
                        @endforeach
                    </ul>
                @endif
            </article>
        @endforeach
    </section>
@endsection
