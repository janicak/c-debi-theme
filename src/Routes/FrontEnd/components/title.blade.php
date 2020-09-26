@spaceless()
<div class="title">@if(!$link){!! $title !!}@if($is_new)<span class="new">NEW!</span>@endif
    @elseif ($modal)
        <a href="{{$permalink}}" data-id={{$id}} rel="modal">{!! $title !!}</a>

    @elseif ($ext && $external_url)
        <a href="{{$external_url}}" target="_blank">{!! $title !!}</a>

    @else
        <a href="{{$permalink}}">{!! $title !!}</a>

    @endif
</div>
@endspaceless()