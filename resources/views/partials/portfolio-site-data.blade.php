@php
    $portfolioSiteData = [
        'resume' => asset($portfolio['resume']),
        'resumeFilename' => basename($portfolio['resume']),
        'github' => $portfolio['profile']['github'],
        'name' => $portfolio['profile']['name'],
        'linkedin' => $portfolio['profile']['linkedin'],
        'instagram' => $portfolio['profile']['instagram'],
    ];
@endphp
<script type="application/json" id="portfolio-site-data">@json($portfolioSiteData)</script>
