@spaceless()
@php
    $label = count($projects) > 1 ? 'Data Projects' : 'Data Project'
@endphp
<div class="project">
    <span class="label">{{$label}}: </span>
    @foreach($projects as $project)
        <a href="{{get_permalink($project->post_id)}}">{{$project->post_title}}</a>
    @endforeach
</div>
@endspaceless()