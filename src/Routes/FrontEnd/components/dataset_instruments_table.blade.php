@spaceless()
@php $base_url = get_site_url(); @endphp
<div class="c-debi-accordion">
    @foreach($instruments as $instrument)
        @php
            $type_term = get_term($instrument['type_term'], 'instrument');
            $type_id = $type_term->term_id;
            $type_name = $type_term->name;
            $type_desc = $instrument['type_desc'];

            $name = $instrument['name'];
            $desc = $instrument['desc'];
        @endphp
        <div class="section">
            <div class="section-title">
                <div class="instrument-title">
                    @if($name)
                        {{$name}} [<a href="{{$base_url}}/?s=&instrument_id={{$type_id}}">{!! $type_name !!}</a>]
                    @else
                        <a href="{{$base_url}}/?s=&instrument_id={{$type_id}}">{!! $type_name !!}</a>
                    @endif
                </div>
                <div class="details"><span class="label">Details</span><span class="toggle"></span></div>
            </div>
            <div class="section-content">
                @if($desc)
                    @php $desc_label = $name ? ' ('.$name.')' : ''; @endphp
                    <div class="instance-description">
                        <div class="name">Instance Description{!! $desc_label !!}</div>
                        <div class="description">{!! $desc !!}</div>
                    </div>
                @endif
                @if($type_desc)
                    <div class="type-description">
                        <div class="name">
                            <a href="{{$base_url}}/?s=&instrument_id={{$type_id}}">{!! $type_name !!}</a>
                        </div>
                        <div class="description">{!! $type_desc !!}</div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endspaceless()