@spaceless()

@php
    $people = $entity->get_people();
    $slugs = $entity->get_post_type_and_term_slugs();
    $display_people = count(array_intersect(['publication', 'award', 'data_project', 'dataset', 'protocol'], $slugs ))
        && count($people);
    $title_link_external = count(array_intersect(['publication', 'newsletter', 'protocol'], $slugs )) ? true : false;
    $show_date = count(array_intersect(['dataset', 'data_project'], $slugs )) ? false : true;
@endphp

<div class="item-header background-color {{ implode( ' ', $slugs  ) }}">
    <div class="container">

        @include('Frontend.components.query_types', ['query_types' => $entity->get_query_type_info()])

        @include('Frontend.components.organization', ['link' => true, 'organization' => $entity->get_organization_info()])

        @include('Frontend.components.title', [
            'title' => $entity->post_title, 'id' => $entity->post_id, 'is_new' => $entity->is_new(),
            'link' => true, 'ext' => $title_link_external, 'modal' => false,
            'external_url' => $entity->get_external_url(), 'permalink' => $entity->permalink
        ])

        <div class="info">

            @if($entity->post_type === 'dataset')
                @include('Frontend.components.dataset_project_link', ['projects' => $entity->related_entities['data_project']])
            @endif

            @if($display_people)
                @include('Frontend.components.people_links', [
                    'people' => $people, 'post_type' => $entity->post_type, 'display' => 'full'
                ])
            @endif

            @if($entity->post_type === 'award' && $entity->acf_fields['award_amount'])
                @include('Frontend.components.award_amount', ['amount' => $entity->acf_fields['award_amount']])
            @endif

            @include('Frontend.components.date', ['date_info' => $entity->get_date_info()])

            @if($entity->post_type === 'publication' && $entity->acf_fields['publication_contribution_number'])
                @include('Frontend.components.publication_contribution_number', [
                    'contribution_number' => $entity->acf_fields['publication_contribution_number']
                ])
            @endif

        </div>

    </div>
</div>
@endspaceless()