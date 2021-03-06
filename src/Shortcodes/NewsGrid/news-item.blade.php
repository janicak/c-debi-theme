@spaceless
@php
$data_url = $slug === 'spotlight' ? ' data-url="'.$entity->permalink.'"' : '';
@endphp
<div class="item" data-id="{{$entity->post_id}}"{{$data_url}}>
    <div class="icon"></div>
    <div class="content">
        @include('FrontEnd.components.organization', ['link' => false, 'organization' => $entity->get_organization_info()])
        @include('FrontEnd.components.title', ['link' => false, 'title' => $entity->post_title, 'is_new' => $entity->is_new()])
        @include('FrontEnd.components.date', ['date_info' => $entity->get_date_info()])
    </div>
</div>
@endspaceless