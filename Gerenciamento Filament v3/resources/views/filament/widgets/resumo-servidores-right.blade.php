<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">{{ static::$heading }}</x-slot>

        @php
            $totais = $this->totais ?? ['geral' => 0, 'por_regime' => []];
            $fmt = fn (int $n) => number_format($n, 0, ',', '.');
            $palette = ['#3b82f6','#f59e0b','#10b981','#ef4444','#8b5cf6','#06b6d4','#84cc16','#f97316'];
            $regimes = array_keys($totais['por_regime']);
            $map = [];
            foreach ($regimes as $i => $r) $map[$r] = $palette[$i % count($palette)];
            $soma = array_sum($totais['por_regime']);
        @endphp

        @if(empty($totais['por_regime']))
            <div class="flex items-center justify-center py-6 text-sm text-gray-500 dark:text-gray-400">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-5.5m0 0a2.5 2.5 0 105 0h-5.5z"/>
                </svg>
                Sem dados para exibir
            </div>
        @else
            {{-- Total geral --}}
            <div class="group relative overflow-hidden rounded-xl border border-gray-200 
                        bg-gradient-to-br from-gray-50 to-gray-100/80 p-4 shadow-sm 
                        dark:border-gray-700 dark:from-gray-800 dark:to-gray-800/60">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl 
                                    bg-primary-100 text-primary-600 dark:bg-primary-900/40 dark:text-primary-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Total de Servidores
                            </span>
                            <span class="text-xl font-bold tabular-nums text-gray-900 dark:text-gray-100">
                                {{ $fmt($totais['geral']) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 dark:text-gray-500">
                        {{ $fmt(count($totais['por_regime'])) }} regimes
                    </div>
                </div>
            </div>

            {{-- Distribuição por regime --}}
            <div class="mt-4 space-y-3">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Distribuição por Regime Contratual</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-3">
                    @foreach($totais['por_regime'] as $regime => $qtd)
                        @php
                            $cor = $map[$regime] ?? '#3b82f6';
                            $pct = $soma > 0 ? round(($qtd / $soma) * 100, 1) : 0;
                        @endphp
                        <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm 
                                    dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="block h-3 w-3 rounded-full" style="background: {{ $cor }}"></span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $regime }}</span>
                                </div>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">
                                    {{ $pct }}%
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold tabular-nums text-gray-800 dark:text-gray-200">{{ $fmt($qtd) }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">servidores</span>
                            </div>
                            <div class="mt-2 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full" style="background: {{ $cor }}; width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
