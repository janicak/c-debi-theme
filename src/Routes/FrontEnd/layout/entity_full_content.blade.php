@spaceless()

<div class="item-body">
    <div class="container">
        <div class="content">
            @if(!in_array($entity->post_type, ['dataset', 'data_project']))

                @if(in_array($entity->post_type, ['award', 'publication']))
                    <h4>Abstract</h4>
                @endif

                {!! apply_filters('the_content', $entity->post_content) !!}

                @php
                    $url = $entity->get_external_url();
                    $label = 'Source';
                    if (!$url) {
                        $url = $entity->acf_fields['publication_file'] ?? false;
                        $label = 'File';
                    }
                @endphp

                @if($url)
                        @include('Frontend.components.source_link', ['url' => $url, 'label' => $label])
                @endif

            @elseif($entity->post_type === 'dataset')
                @include('Frontend.layout.dataset_content', ['entity' => $entity ] )

            @elseif($entity->post_type === 'data_project')
                @include('Frontend.layout.data_project_content', ['entity' => $entity, 'embedded' => false ] )
            @endif

            @php
                $related_entities = array_filter($entity->get_related_entities(), function($entities){ return count($entities); });
            @endphp

            @if(count($related_entities))
                <div class="related">
                    @include('Frontend.components.content_section_header', ['tag' => 'h4', 'value' => 'Related Items'])
                    @include('Frontend.layout.related_entities', ['entities' => $related_entities])
                </div>
            @endif

        </div>
    </div>
</div>
@endspaceless()