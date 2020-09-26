@spaceless()
@php
    [ 'slug' => $slug, 'name' => $name ] = $organization;
    $base_url = get_site_url();
@endphp
<div class="organization">

    @if($link && $slug)
        <a href="{{$base_url}}/?s=&organization={{$slug}}">{{$name}}</a>
    @else
        {{$name}}
    @endif

</div>
@endspaceless()