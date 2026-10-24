<article class="portfolio-card project-card">
    @if(!empty($project['image']))
        <img class="portfolio-project-image" src="{{ asset($project['image']) }}" alt="{{ $project['title'] }} project preview" loading="lazy" width="640" height="320">
    @else
        <div class="portfolio-project-brand" aria-hidden="true">{{ $project['brand'] }}</div>
    @endif
    <h2>{{ $project['title'] }}</h2>
    <p>{{ $project['description'] }}</p>
    @if(!empty($project['context']))
        <p class="project-context">{{ $project['context'] }}</p>
    @endif
    @if(!empty($project['technologies']))
        <p><strong>Tools:</strong> {{ implode(', ', $project['technologies']) }}</p>
    @endif
    @if(!empty($project['url']))
        <a href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer">{{ $project['link_label'] ?? 'Live site' }} <i class="fas fa-external-link-alt" aria-hidden="true"></i></a>
    @endif
</article>
