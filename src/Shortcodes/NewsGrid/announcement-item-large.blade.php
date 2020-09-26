@spaceless
<div class="item" data-id="{{$entity->post_id}}">
    <div class="icon"></div>
    <div class="content">
        @include('FrontEnd.components.title', ['link' => false, 'title' => $entity->post_title, 'is_new' => $entity->is_new()])
        @include('FrontEnd.components.date', ['date_info' => $entity->get_date_info()])
        <div class="content">
            {!! apply_filters('the_content', $entity->post_content) !!}
        </div>

    </div>
</div>
@endspaceless