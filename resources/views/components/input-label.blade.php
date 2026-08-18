@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-[11px] font-bold text-gray-500 uppercase tracking-wider']) }}>
    {{ $value ?? $slot }}
</label>
