@spaceless()
@php $base_url = get_site_url(); @endphp
<div class="instruments">
    <span class="label">Instruments: </span>
    @foreach($instruments as $instrument)
        @php
            $type_term = get_term($instrument['type_term'], 'instrument');
            $type_id = $type_term->term_id;
            $type_name = $type_term->name;
        @endphp
        <a href="{{$base_url}}/?s=&instrument_id={{$type_id}}">{!! $type_name !!}</a>@if(!$loop->last), @endif
    @endforeach
</div>

@endspaceless()