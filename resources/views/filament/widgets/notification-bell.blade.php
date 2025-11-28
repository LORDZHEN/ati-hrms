<div x-data="{ open: false }" class="relative">
    <!-- Bell Icon -->
    <button @click="open = !open" class="relative focus:outline-none">
        <x-heroicon-o-bell class="w-6 h-6 text-gray-600 dark:text-gray-300" />

        @if($unreadCount)
            <span
                class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg rounded-lg z-50">

        <div class="p-2">
            <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Notifications</h3>

            @forelse($notifications as $notification)
                <div class="p-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <div>
                        <p class="{{ is_null($notification->read_at) ? 'font-bold' : '' }} text-gray-700 dark:text-gray-100">
                            {{ $notification->data['message'] ?? 'No message' }}
                        </p>
                        <small class="text-gray-500 dark:text-gray-400 text-xs">
                            {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>
                    @if(is_null($notification->read_at))
                        <button wire:click="markAsRead({{ $notification->id }})"
                                class="text-blue-500 dark:text-blue-400 text-xs">
                            Mark as read
                        </button>
                    @endif
                </div>
            @empty
                <p class="p-2 text-gray-500 dark:text-gray-400 text-sm">No notifications</p>
            @endforelse
        </div>
    </div>
</div>
