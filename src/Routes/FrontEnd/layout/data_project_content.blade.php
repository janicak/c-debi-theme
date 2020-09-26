@spaceless()

@php
    $bco_dmo_metadata_field_config = [
        'post_title' => ['label' => 'Project Title', 'type' => 'post_title'],
        'data_project_acronym' => ['label' => 'Acronym', 'type' => 'text'],
        'data_project_url' => ['label' => 'URL', 'type' => 'link'],
        'data_project_date_created' => ['label' => 'Created', 'type' => 'date'],
        'data_project_date_modified' => ['label' => 'Modified', 'type' => 'date'],
    ];
@endphp

@include('Frontend.components.bco_dmo_metadata_table', [ 'entity' => $entity, 'field_config' => $bco_dmo_metadata_field_config ])

@php
    $description_fields = [];
    $description_field_names_to_labels = [
        'data_project_description' => 'Project Description',
    ];
    foreach($description_field_names_to_labels as $field_name => $label){
        if (isset($entity->acf_fields[$field_name])){
            $description_fields[$field_name] = [
                'label' => $label,
                'value' => $entity->acf_fields[$field_name],
            ];
        }
    }
@endphp

@foreach($description_fields as $field_name => $field_info)
    @php
        ['label' => $label, 'value' => $value] = $field_info;
        $header_tag = $embedded ? 'h5' : 'h4';
    @endphp
    @include('Frontend.components.content_section_header', ['tag' => $header_tag, 'value' => $label])
    @include('Frontend.components.bco_dmo_description_field', ['value' => $value, 'field_name' => $field_name])
@endforeach

@php
    $people = $entity->get_people();
@endphp

@if(count($people))
    @foreach($people as $field)
        @php
            ['label' => $field_label, 'people' => $field_rows] = $field;
            $header_tag = $embedded ? 'h5' : 'h4';
        @endphp
        @if($field_rows)
            @include('Frontend.components.content_section_header', ['tag' => $header_tag, 'value' => $field_label])
            @include('Frontend.components.bco_dmo_people_table', [
                'field_rows' => $field_rows, 'post_type' => 'data_project'
            ])
        @endif
    @endforeach
@endif

@endspaceless()