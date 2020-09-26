@spaceless()

@foreach($entities as $entity_slug => $items)
    @if(count($items))
        @php
            $label = $items[0]->post_type_labels['plural']
        @endphp
        <div class="related-category {{$entity_slug}}">
            <div class="container">
                <div class="title">
                    <span class="icon"></span><span>{{$label}}</span>
                </div>
                <div class="items">
                    @foreach($items as $item)
                        <div class="item">
                            @include('FrontEnd.layout.entity_search_result', ['entity' => $item, 'title_link_modal' => false])
                        </div>
                        @if(!$loop->last)
                            <div class="separator"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endforeach
@endspaceless()