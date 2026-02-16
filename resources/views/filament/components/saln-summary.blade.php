<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total Assets Card --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-success-50 dark:bg-success-900/10 p-4">
            <div class="flex items-center gap-2 text-sm font-medium text-success-600 dark:text-success-400 mb-1">
                <x-heroicon-o-building-office class="w-5 h-5" />
                <span>Total Assets</span>
            </div>
            <div class="text-2xl font-bold text-success-700 dark:text-success-300">
                ₱{{ number_format($totalAssets, 2) }}
            </div>
        </div>

        {{-- Total Liabilities Card --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-danger-50 dark:bg-danger-900/10 p-4">
            <div class="flex items-center gap-2 text-sm font-medium text-danger-600 dark:text-danger-400 mb-1">
                <x-heroicon-o-credit-card class="w-5 h-5" />
                <span>Total Liabilities</span>
            </div>
            <div class="text-2xl font-bold text-danger-700 dark:text-danger-300">
                ₱{{ number_format($totalLiabilities, 2) }}
            </div>
        </div>

        {{-- Net Worth Card --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-{{ $netWorthColor }}-50 dark:bg-{{ $netWorthColor }}-900/10 p-4">
            <div class="flex items-center gap-2 text-sm font-medium text-{{ $netWorthColor }}-600 dark:text-{{ $netWorthColor }}-400 mb-1">
                <x-heroicon-o-banknotes class="w-5 h-5" />
                <span>Net Worth</span>
            </div>
            <div class="text-3xl font-bold text-{{ $netWorthColor }}-700 dark:text-{{ $netWorthColor }}-300">
                ₱{{ number_format($netWorth, 2) }}
            </div>
        </div>
    </div>

    {{-- Calculation Formula --}}
    <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
        Formula: Net Worth = Total Assets - Total Liabilities
    </div>
</div>
