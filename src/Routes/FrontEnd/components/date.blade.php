@spaceless()
@php
    ['label' => $label, 'dates' => $dates ] = $date_info;
    $formatted_dates = implode(' — ', array_map(function($date){ return $date->format('F j, Y');}, $dates));
@endphp
<div class="date-info">
    <span class="label">{{$label}}: </span><span class="date">{{$formatted_dates}}</span>
</div>
@endspaceless()