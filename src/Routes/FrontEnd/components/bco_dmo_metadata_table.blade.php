@spaceless
@php
$fields = [];
foreach($field_config as $field_name => $config){
    ['label' => $label, 'type' => $type] = $config;
    if ($type === 'post_title'){
        $fields[$field_name] = [
            'label' => $label,
            'type' => 'text',
            'value' => $entity->post_title
        ];
    } else if (isset($entity->acf_fields[$field_name])){
        $fields[$field_name] = [
            'label' => $label,
            'type' => $type,
            'value' => $entity->acf_fields[$field_name],
        ];
    }
}
@endphp

@if($fields)
    <table>
        <tbody>
        @foreach($fields as $field)
            @php ['label' => $label, 'type' => $type, 'value' => $value] = $field; @endphp
            <tr>
                <th>{{$label}}</th>
                <td>
                    @switch($type)
                        @case('link')
                        <a href="{{$value}}" target="_blank">{!! $value !!}</a>
                        @break
                        @case('date')
                        {{$value->format('F j, Y')}}
                        @break
                        @case('text') @default
                        {!! $value !!}
                        @break
                    @endswitch
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

@endspaceless