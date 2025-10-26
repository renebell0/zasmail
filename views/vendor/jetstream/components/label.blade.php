@props(['value', 'new' => false])

<label {{ $attributes->merge(['class' => 'flex items-center gap-2 block font-medium text-sm text-gray-700']) }}>
    <span>{{ $value ?? $slot }}</span>
    @if($new)
    <span class="bg-indigo-500 text-white rounded px-2 my-1 text-xxs tracking-widest font-bold">{{ __('NEW') }}</span>
    @endif
</label>
