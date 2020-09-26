@spaceless
@include('FrontEnd.components.loader')
<div class="cdebi-slider">
    @foreach ($slides as $key => $slide)
        @php
            ['title' => $title, 'caption' => $caption, 'link' => $link, 'background' => $background] = $slide;
            $background_style = isset($background['image'])
                ? 'background-image: url(\''.$background['image']['url'].'\')'
                : 'background-color: '.$background['color'];
            $inset = $slide['inset'] ?? false;
        @endphp
        <div class="item {{$key}}">
            <div class="description">
                <div class="title">{!! $title !!}</div>
                <div class="caption">{!! $caption !!}</div>
                <div class="link"><a href="{{$link['url']}}">{!! $link['title'] !!}</a></div>
            </div>
            <div class="background" style="{{$background_style}}">
                <div class="background-spacer"></div>
                @if($inset)
                    <div class="inset">
                        <div class="inset-images">
                            <div class="container">
                                @foreach($inset['images'] as $inset_img_url)
                                    <img class="inset-image" data-lazy="{{$inset_img_url}}"/>
                                    @if(!$loop->last)<span class="separator"></span>@endif
                                @endforeach
                            </div>
                            @if(isset($inset['caption']))
                                <div class="inset-caption">{!! $inset['caption']!!}</div>
                            @endif
                        </div>
                    </div>
                @endif
                @if(isset($background['caption']))
                    <div class="background-caption">{!!  $background['caption'] !!}</div>
                @else
                    <div class="background-spacer"></div>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endspaceless