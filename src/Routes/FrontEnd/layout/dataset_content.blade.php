@spaceless()

@php
    $bco_dmo_metadata_field_config = [
        'dataset_url' => ['label' => 'URL', 'type' => 'link'],
        'dataset_download_url' => ['label' => 'Download URL', 'type' => 'link'],
        'dataset_media_type' => ['label' => 'Media Type', 'type' => 'text'],
        'dataset_date_created' => ['label' => 'Created', 'type' => 'date'],
        'dataset_date_modified' => ['label' => 'Modified', 'type' => 'date'],
        'dataset_bco_dmo_state' => ['label' => 'State', 'type' => 'text'],
        'dataset_brief_description' => ['label' => 'Brief Description', 'type' => 'text'],
    ];
@endphp

@include('Frontend.components.bco_dmo_metadata_table', [ 'entity' => $entity, 'field_config' => $bco_dmo_metadata_field_config ])

@php
    $description_fields = [];
    $description_field_names_to_labels = [
        'dataset_acquisition_description' => 'Acquisition Description',
        'dataset_processing_description' => 'Processing Description',
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

@foreach($description_fields as $field_name => $config)
    @php ['label' => $label, 'value' => $value] = $config; @endphp
    @include('Frontend.components.content_section_header', ['tag' => 'h4', 'value' => $label])
    @include('Frontend.components.bco_dmo_description_field', ['value' => $value, 'field_name' => $field_name])
@endforeach

@if(count($entity->acf_fields['dataset_instruments']))
    @include('Frontend.components.content_section_header', ['tag' => 'h4', 'value' => 'Instruments'])
    @include('Frontend.components.dataset_instruments_table', [
        'instruments' => $entity->acf_fields['dataset_instruments']
    ])
@endif

@if(count($entity->acf_fields['dataset_parameters']))
    @include('Frontend.components.content_section_header', ['tag' => 'h4', 'value' => 'Parameters'])
    @include('Frontend.components.dataset_parameters_table', [
        'parameters' => $entity->acf_fields['dataset_parameters']
    ])
@endif

@php
    $people = $entity->get_people();
@endphp

@foreach($people as $field)
    @php ['label' => $field_label, 'people' => $field_rows] = $field; @endphp
    @if($field_rows && $field_label === 'Dataset Maintainers')
        @include('Frontend.components.content_section_header', ['tag' => 'h4', 'value' => $field_label])
        @include('Frontend.components.bco_dmo_people_table', [
            'field_rows' => $field_rows, 'post_type' => 'dataset'
        ])
    @endif
@endforeach

@php
    $data_projects = $entity->get_related_entities('data_project')
@endphp

@foreach($data_projects as $data_project)
    @include('Frontend.components.content_section_header', ['tag' => 'h4', 'value' => 'BCO-DMO Project Info'])
    @include('Frontend.layout.data_project_content', ['entity' => $data_project, 'embedded' => true ])
@endforeach

@endspaceless()