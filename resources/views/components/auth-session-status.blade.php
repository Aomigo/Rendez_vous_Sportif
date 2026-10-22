@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-semibold text-sm text-green-700']) }}>
        {{ $status }}
    </div>
@endif
