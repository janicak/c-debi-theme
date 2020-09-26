@spaceless
@php

@endphp
@include('FrontEnd.components.organization', ['link' => false, 'organization' => $entity->get_organization_info()]);
@include('FrontEnd.components.title', [
            'title' => $entity->post_title, 'id' => $entity->post_id,
            'is_new' => $entity->is_new(), 'link' => false
        ])
@include('FrontEnd.components.date', ['date_info' => $entity->get_date_info()])
@endspaceless