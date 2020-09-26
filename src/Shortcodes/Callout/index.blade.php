@spaceless
@if($content && $classname)
    <div class="{{$classname}}">
        <div class="content">{!! $content !!}</div>
    </div>
@endif
@endspaceless