@spaceless()
@php
    $slugs = $entity->get_post_type_and_term_slugs();
    $date_info = $entity->get_date_info();
    $sort_date = $date_info['sort_date'];
    $search_weight = $entity->search_result_weight;
    $query_types = $entity->get_query_type_info();

    $show_organization = count(array_intersect(['spotlight', 'announcement'], $slugs )) ? false : true;
    $title_link_external = count(array_intersect(['newsletter', 'protocol'], $slugs )) ? true : false;
    $title_link_modal = isset($title_link_modal) && $title_link_modal && !$title_link_external;
    $show_excerpt = isset($show_excerpt) ?? false;

    $people = $entity->get_people();

@endphp
<article class="search-result {{implode(' ', $slugs)}}" data-sort="{{$sort_date}}" data-weight="{{$search_weight}}">
    <div class="container">

        <div class="head">
            @include('Frontend.components.query_types', ['query_types' => $query_types])
            @include('Frontend.components.date', ['date_info' => $date_info])
        </div>

        @if($show_organization)
            @include('Frontend.components.organization', ['link' => true, 'organization' => $entity->get_organization_info()])
        @endif

        @if($entity->post_type === 'dataset' && $entity->acf_fields['dataset_instruments'])
            @include('Frontend.components.dataset_instrument_links', ['instruments' => $entity->acf_fields['dataset_instruments']])
        @endif

        @include('Frontend.components.title', [
            'title' => $entity->post_title, 'id' => $entity->post_id, 'is_new' => $entity->is_new(),
            'link' => true, 'ext' => $title_link_external, 'modal' => $title_link_modal,
            'external_url' => $entity->get_external_url(), 'permalink' => $entity->permalink
        ])

        @if($entity->post_type === 'dataset')
            @php
                $data_projects = $entity->get_related_entities('data_project')
            @endphp
            @if(count($data_projects))
                @include('Frontend.components.dataset_project_link', ['projects' => $data_projects])
            @endif
        @endif

        @if(count($people))
            @include('Frontend.components.people_links', [
                'people' => $people, 'post_type' => $entity->post_type, 'display' => 'short'
            ])
        @endif

        @if($entity->post_type === 'publication' && $entity->acf_fields['publication_contribution_number'])
            @include('Frontend.components.publication_contribution_number', ['contribution_number' => $entity->acf_fields['publication_contribution_number']])
        @endif

        @if($show_excerpt)
            @php $excerpt = $entity->get_search_excerpt() @endphp
            @if($excerpt)
                <div class="excerpt">{!! $excerpt !!}</div>
            @endif
        @endif
    </div>
</article>

@endspaceless()