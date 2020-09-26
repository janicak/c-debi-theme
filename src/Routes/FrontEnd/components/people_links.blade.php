@spaceless()
@php $base_url = get_site_url(); @endphp
@if($people)
    @foreach($people as $field)
        @php
            ['label' => $field_label, 'people' => $field_rows] = $field;
            $field_rows = is_array($field_rows) ? array_filter($field_rows, function($row){ return isset($row['person']) && $row['person']; }) : false;
        @endphp

        @if($field_rows)

            @switch($post_type)
                @case('publication') @case('protocol')
                <div class="people">
                    <span class="label">{{$field_label}}: </span>

                    @foreach($field_rows as $i => $row)
                        @php $separator = $i + 1 < count($field_rows) ? ', ' : ''; @endphp
                        <a href="{{$base_url}}/?s=&person_id={{$row['person']->ID}}">{{$row['person']->post_title}}</a>{{$separator}}
                    @endforeach
                </div>

                @break

                @case('award')
                <div class="people {{$field_label}}">
                    <span class="label">{{$field_label}}: </span>
                    <span class="value">
                        @foreach($field_rows as $i => $row)
                            @php
                                $organization = $row['organization'] ? "&nbsp;(".$row['organization'].")" : "";
                                $separator = $i + 1 < count($field_rows) ? ', ' : '';
                            @endphp
                            <a href="{{$base_url}}/?s=&person_id={{$row['person']->ID}}">{{$row['person']->post_title}}</a>{{$organization}}{{$separator}}
                            @if(isset($row['current_placement']) && $display === 'full')
                                <div class="current-placement">
                                        <span class="label">Current Placement: </span>{{$row['current_placement']}}
                                    </div>
                            @endif
                            @if(isset($row['degree']) && $display === 'full')
                                <div class="degree">
                                        <span class="label">Degree: </span>{{$row['degree']}}
                                    </div>
                            @endif
                        @endforeach
                    </span>
                </div>

                @break

                @case('dataset') @case('data_project')
                <div class="people">
                    <span class="label">{{$field_label}}: </span>

                    @foreach($field_rows as $i => $row)
                        @php $separator = $i + 1 < count($field_rows) ? ', ' : ''; @endphp
                        <a href="{{$base_url}}/?s=&person_id={{$row['person']->ID}}">{{$row['person']->post_title}}</a>{{$separator}}
                    @endforeach
                </div>
                @break

                @default
                @break
            @endswitch

        @endif

    @endforeach
@endif
@endspaceless()