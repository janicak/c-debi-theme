@spaceless
@if($categories["announcement"])
    <div class="announcements">
        <div class="announcement">
            <div class="title announcement background-color">
                <span><span class="icon"></span><span>Announcements</span></span>
            </div>
            <div class="container">
                <div class="items">
                    @foreach($categories["announcement"]["posts"] as $entity)
                        @if($loop->first)
                            @include('NewsGrid.announcement-item-large', ['entity' => $entity])
                        @else
                            @include('NewsGrid.news-item', ['entity' => $entity, 'slug' => 'announcement'])
                        @endif
                        @if(!$loop->last)
                            <div class="separator"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @foreach($categories["announcement"]["posts"] as $entity)
            @include('NewsGrid.news-item-modal', ['entity' => $entity])
        @endforeach
    </div>
@endif
<div class="news-grid">
    <div class="grid-sizer"></div>
    @foreach($categories as $slug => ['label' => $label, 'posts' => $entities])
        <div class="category {{$slug}}">
            <div class="container">
                <div class="title {{$slug}} background-color">
                    <span>
                        <span class="icon"></span>
                        <span>{{$label}}</span>
                    </span>
                </div>
                <div class="items">
                    @foreach($entities as $entity)
                        @include('NewsGrid.news-item', ['entity' => $entity, 'slug' => $slug])
                        @if(!$loop->last)
                            <div class="separator"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @foreach($entities as $entity)
            @include('NewsGrid.news-item-modal', ['entity' => $entity])
        @endforeach
    @endforeach
</div>
@endspaceless