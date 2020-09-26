@spaceless()
@php $base_url = get_site_url(); @endphp
<div class="c-debi-accordion">
    @foreach($parameters as $parameter)
        @php
            [   'generic_name' =>  $generic_name,
                'generic' => $generic_id,
                'generic_desc' => $generic_desc,
                'name' => $name,
                'desc' => $desc
            ] = $parameter;
        @endphp
        <div class="section">
            <div class="section-title">
                <div class="instrument-title">
                    {{$name}} [<a href="{{$base_url}}/?s=&paramter_id={{$generic_id}}">{!! $generic_name !!}</a>]
                </div>
                <div class="details"><span class="label">Details</span><span class="toggle"></span></div>
            </div>
            <div class="section-content">
                @if($desc)
                    <div class="instance-description">
                        <div class="name">{!! $name !!}</div>
                        <div class="description">{!! $desc !!}</div>
                    </div>
                @endif
                @if($generic_desc)
                    <div class="generic-description">
                        <div class="name">
                            <a href="{{$base_url}}/?s=&paramter_id={{$generic_id}}">{!! $generic_name !!}</a>
                        </div>
                        <div class="description">{!! $generic_desc !!}</div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

@endspaceless()