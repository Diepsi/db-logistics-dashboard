@php
    $normalized = strtoupper(trim((string) ($status ?? '')));

    $toneMap = [
        'COMPLETED' => 'emerald',
        'DELIVERED' => 'emerald',
        'SELESAI' => 'emerald',
        'ON DELIVERY' => 'amber',
        'IN TRANSIT' => 'amber',
        'PENDING' => 'amber',
        'PROCESSING' => 'amber',
        'PROSES' => 'amber',
        'UNDELIVERED' => 'rose',
        'RETURN TO HO' => 'rose',
        'FAILED' => 'rose',
        'GAGAL' => 'rose',
        'OVER SLA' => 'rose',
    ];

    $tone = $toneMap[$normalized] ?? ($tone ?? 'gray');

    $tones = [
        'emerald' => ['badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20', 'dot' => 'bg-emerald-500'],
        'amber' => ['badge' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-500/20', 'dot' => 'bg-amber-500'],
        'rose' => ['badge' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-300 dark:border-rose-500/20', 'dot' => 'bg-rose-500'],
        'gray' => ['badge' => 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-500/10 dark:text-gray-300 dark:border-gray-500/20', 'dot' => 'bg-gray-400'],
    ];

    $active = $tones[$tone];
@endphp

<span {{ $attributes->merge(['class' => 'badge border '.$active['badge']]) }}>
    <span class="dot {{ $active['dot'] }}"></span>
    {{ $slot ?? $status }}
</span>
