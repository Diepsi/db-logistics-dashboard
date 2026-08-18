<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg font-semibold text-sm text-gray-600 shadow-sm hover:bg-gray-50 hover:text-gray-800 hover:border-gray-300 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-dbl-green/30 focus:ring-offset-1 disabled:opacity-50 transition-all duration-150']) }}>
    {{ $slot }}
</button>
