<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">{{ static::$heading }}</x-slot>

        @php
        $totais = $this->totais ?? ['geral' => 0, 'por_tipo' => []];
        $stats = $this->getEstatisticas();
        $fmt = fn (int $n) => number_format($n, 0, ',', '.');
        $palette = ['#3b82f6','#f59e0b','#10b981','#ef4444','#8b5cf6','#06b6d4','#84cc16','#f97316'];
        $tipos = array_keys($totais['por_tipo']);
        $map = [];
        foreach ($tipos as $i => $t) $map[$t] = $palette[$i % count($palette)];
        $soma = array_sum($totais['por_tipo']);
        @endphp

        @if(empty($totais['por_tipo']))
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                Nenhum atestado encontrado
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">
                Quando houver atestados cadastrados, as estatísticas por tipo aparecerão aqui.
            </p>
        </div>
        @else
        {{-- Cartão principal com total geral --}}
        <div class="group relative overflow-hidden rounded-xl border border-gray-200 
                        bg-gradient-to-br from-blue-50 to-indigo-50/80 p-5 shadow-sm 
                        dark:border-gray-700 dark:from-blue-900/20 dark:to-indigo-900/10 mb-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl 
                                    bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Total de Atestados Registrados
                        </span>
                        <span class="text-3xl font-bold tabular-nums text-gray-900 dark:text-gray-100">
                            {{ $fmt($totais['geral']) }}
                        </span>
                    </div>
                </div>
                <div class="text-right space-y-2">
                    <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                        {{ $fmt($stats['diversidade']) }} tipos diferentes
                    </div>
                    @if($stats['tipo_mais_comum'])
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/80 dark:bg-gray-800/80 text-sm font-medium shadow-sm">
                        <span class="h-3 w-3 rounded-full" style="background: {{ $map[$stats['tipo_mais_comum']] ?? '#3b82f6' }}"></span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $stats['concentracao'] }}% {{ $stats['tipo_mais_comum'] }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Estatísticas rápidas --}}
        @if($stats['tipo_mais_comum'])
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-3 mb-4">
            {{-- Tipo mais comum --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm 
                                dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                        <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        Mais Frequente
                    </span>
                </div>
                <div class="space-y-1">
                    <div class="text-lg font-bold text-gray-900 dark:text-gray-100 truncate" title="{{ $stats['tipo_mais_comum'] }}">
                        {{ $stats['tipo_mais_comum'] }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $fmt($totais['por_tipo'][$stats['tipo_mais_comum']]) }} atestados registrados
                    </div>
                </div>
            </div>

            {{-- Concentração --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm 
                                dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/30">
                        <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        Taxa de Concentração
                    </span>
                </div>
                <div class="space-y-1">
                    <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        {{ $stats['concentracao'] }}%
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        concentrados no tipo principal
                    </div>
                </div>
            </div>

            {{-- Diversidade --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm 
                                dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                        <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        Índice de Diversidade
                    </span>
                </div>
                <div class="space-y-1">
                    <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        {{ $fmt($stats['diversidade']) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        tipos diferentes cadastrados
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Distribuição detalhada por tipo --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Distribuição por Tipo de Atestado
                </h4>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ count($totais['por_tipo']) }} tipos
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach(collect($totais['por_tipo'])->sortDesc() as $tipo => $qtd)
                @php
                $cor = $map[$tipo] ?? '#3b82f6';
                $pct = $soma > 0 ? round(($qtd / $soma) * 100, 1) : 0;
                $isDestaque = $tipo === $stats['tipo_mais_comum'];
                @endphp
                <div class="group relative rounded-lg border transition-all duration-200 hover:shadow-md
                    {{ $isDestaque 
                       ? 'border-blue-200 bg-gradient-to-r from-blue-50 to-blue-50/30 dark:border-blue-800 dark:from-blue-900/20 dark:to-blue-900/5' 
                       : 'border-gray-200 bg-white hover:border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-gray-600' 
                    }} p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <span class="block h-4 w-4 rounded-full shadow-sm flex-shrink-0" style="background: {{ $cor }}"></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-base font-semibold text-gray-900 dark:text-gray-100 block truncate" title="{{ $tipo }}">
                                        {{ $tipo }}
                                    </span>
                                    @if($isDestaque)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 flex-shrink-0">
                                        Mais frequente
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end text-right">
                            <span class="text-xl font-bold tabular-nums text-gray-800 dark:text-gray-200">
                                {{ $fmt($qtd) }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $pct }}%</span>
                        </div>
                    </div>
                    <div class="h-3 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 ease-out"
                            style="background: {{ $cor }}; width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>