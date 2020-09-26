@spaceless()

@php
    [ 'param' => $param, 'values' => $values ] = $query_types;
@endphp

<div class="type">
    @foreach($values as $value)
        @php
            [ 'slug' => $slug, 'label' => $label ] = $value;
            $sub_type = $value[ 'sub_type' ] ?? false;
            $base_url = get_site_url();
        @endphp

        <span class="{{$slug}}">
            <span class="icon"></span>
                <a href="{{$base_url}}/?s=&{{$param}}={{$slug}}">{{$label}}</a>

                @if( $sub_type )

                <span class="separator">&nbsp;>&nbsp;</span>

                @foreach($sub_type as $sub_value)
                    @php [ 'param' => $sub_type_param, 'slug' => $sub_type_slug, 'label' => $sub_type_label ] = $sub_value; @endphp

                    <a href="{{$base_url}}/?s=&{{$sub_type_param}}={{$sub_type_slug}}">{{$sub_type_label}}</a>

                    @if(!$loop->last)
                        <span class="separator">,&nbsp;</span>
                    @endif

                @endforeach

            @endif
        </span>

        @if(!$loop->last)
            <span class="separator">, </span>
        @endif

    @endforeach
</div>
@endspaceless()