@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full text-sm rounded-lg border-gray-300 bg-white text-gray-800 shadow-sm focus:border-dbl-green focus:ring-dbl-green/30 focus:ring-2 transition-shadow']) }}>
