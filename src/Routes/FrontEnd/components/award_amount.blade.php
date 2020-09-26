@spaceless()
@php
    $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    $formatted_amount = $formatter->formatCurrency($amount, 'USD');
@endphp
<div class="amount">
    <span class="label">Amount: </span><span class="amount">{{$formatted_amount}}</span>
</div>
@endspaceless()