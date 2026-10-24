@extends('layouts.portfolio')
@section('title', 'Experience')
@section('description', "Sanish Maharjan's PHP, Laravel, Node.js, and full-stack engineering experience.")

@section('content')
    @include('partials.portfolio-heading', [
        'section' => 'Experience',
        'heading' => 'Professional experience',
        'description' => 'Backend engineering, real-time data systems, and full-stack delivery across advertising, financial services, and digital products.',
    ])
    <div class="experience-list">
        @foreach($portfolio['experience'] as $experience)
            <article class="portfolio-card experience-card">
                <div class="experience-company">
                    @if(!empty($experience['image']))
                        <img src="{{ asset($experience['image']) }}" alt="{{ $experience['company'] }} logo" width="160" height="88">
                    @else
                        <span class="experience-wordmark">{{ $experience['brand'] ?? $experience['company'] }}</span>
                    @endif
                    <div>
                        <h2>{{ $experience['company'] }}</h2>
                        <p class="experience-location">{{ $experience['location'] }}</p>
                    </div>
                </div>
                <p class="experience-period">{{ $experience['period'] }}</p>
                <h3>{{ $experience['title'] }}</h3>
                <ul>
                    @foreach($experience['responsibilities'] as $responsibility)
                        <li>{{ $responsibility }}</li>
                    @endforeach
                </ul>
            </article>
        @endforeach
    </div>
@endsection
