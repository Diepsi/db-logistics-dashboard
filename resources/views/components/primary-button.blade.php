<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-dbl-green to-dbl-green-dark border border-transparent rounded-lg font-bold text-sm text-white shadow-md shadow-dbl-green/20 transition-all duration-200 hover:shadow-glow hover:brightness-105 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-dbl-green/40 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none']) }}>
    {{ $slot }}
</button>
