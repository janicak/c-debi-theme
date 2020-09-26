@spaceless
<div class="eo-resources">
    <div class="eo-filters">
        @php ksort($filters) @endphp
        @foreach($filters as $slug => ['label' => $label, 'options' => $options])
            <div class="eo-filter">
                <span class="label">Filter by {{$label}}: </span>
                <select data-filter="{{$slug}}">
                    <option value="*">All</option>
                    @foreach($options as $option_slug => $option_label)
                        <option value="{{$option_slug}}">{{$option_label}}</option>
                    @endforeach
                </select>
            </div>
        @endforeach
    </div>
    <div class="c-debi-accordion">
        @foreach($entities as $entity)
            @php
                $url = $entity->acf_fields['eo_resource_url'];
                $audiences = $entity->taxonomies['audience'];
                $audience_slugs = array_map(function($term){ return $term->slug; }, $audiences);
                $resource_types = $entity->taxonomies['resource_type'];
                $resource_type_slugs = array_map(function($term){ return $term->slug; }, $resource_types);
                $organizations = $entity->taxonomies['organization'];
            @endphp

            <div class="section" data-audience="{{implode(" ", $audience_slugs)}}" data-resource_type="{{implode(' ', $resource_type_slugs)}}">
                <div class="section-title">
                    <div class="title">
                        <div class="resource-title">
                            @if($url)
                                <a href="{{$url}}" target="_blank">{!! $entity->post_title !!}</a>
                            @else
                                {!! $entity->post_title !!}
                            @endif
                        </div>
                    </div>
                    <div class="details">
                        <span class="label">Details</span><span class="toggle"></span>
                    </div>
                </div>
                <div class="section-content">
                    @php
                        $metadata = [
                            'organizations' => [
                                'label' => count($organizations) > 1 ? 'Organizations' : 'Organization', 'terms' => $organizations
                            ],
                            'audience' => [
                                'label' => count($organizations) > 1 ? 'Audiences' : 'Audience', 'terms' => $audiences
                            ],
                            'resource-type' => [
                                'label' => count($resource_types) > 1 ? 'Resource Types' : 'Resource Type', 'terms' => $resource_types
                            ]
                        ];
                    @endphp
                    @foreach($metadata as $classname => ['label' => $label, 'terms' => $terms])
                        @if(count($terms))
                            <div class="{{$classname}}">
                                <span class="label">{{$label}}</span>
                                @foreach($terms as $term)
                                    {{$term->name}}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                    @if($entity->post_content)
                        <div class="description">
                            {!! apply_filters('the_content', $entity->post_content) !!}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endspaceless