<div class="flex h-full w-full flex-1 flex-col gap-6">
    {{-- Header with actions --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Vehicles</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Monitor your connected vehicles and their status</p>
        </div>

        @if($hasEnodeConnection)
            <div class="flex gap-2">
                <flux:button href="{{ route('enode.connect') }}" variant="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Vehicle
                </flux:button>

                <flux:button wire:click="refreshAll" :disabled="$isRefreshing" variant="ghost">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh All
                </flux:button>

                <form action="{{ route('enode.disconnect') }}" method="POST" onsubmit="return confirm('Are you sure you want to disconnect your Enode account?')">
                    @csrf
                    <flux:button type="submit" variant="danger">
                        Disconnect Enode
                    </flux:button>
                </form>
            </div>
        @endif
    </div>

    {{-- Alert messages --}}
    @if(session('success'))
        <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Connect Enode CTA --}}
    @if(!$hasEnodeConnection)
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-8 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">Connect Your Vehicles</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                Link your electric vehicle to track battery status, location, odometer readings, and more through Enode.
            </p>
            <flux:button href="{{ route('enode.connect') }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Connect Vehicle
            </flux:button>
        </div>
    @endif

    {{-- Vehicles Grid --}}
    @if($hasEnodeConnection && count($vehicles) > 0)
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($vehicles as $vehicle)
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 overflow-hidden hover:border-neutral-300 dark:hover:border-neutral-600 transition-colors">
                    {{-- Vehicle Header --}}
                    <a href="{{ route('vehicles.detail', $vehicle) }}" class="block">
                        <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $vehicle->make }} {{ $vehicle->model }}
                                    </h3>
                                @if($vehicle->year)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $vehicle->year }}</p>
                                @endif
                                @if($vehicle->vendor)
                                    <span class="inline-flex items-center px-2 py-1 mt-2 text-xs font-medium rounded-md bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ ucfirst($vehicle->vendor) }}
                                    </span>
                                @endif
                            </div>
                            <flux:button wire:click.stop="refreshVehicle({{ $vehicle->id }})" :disabled="$isRefreshing" size="sm" variant="ghost">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </flux:button>
                        </div>
                    </div>
                    </a>

                    {{-- Vehicle Data --}}
                    <div class="p-6 space-y-4">
                        {{-- Battery Status --}}
                        @if($vehicle->battery_level !== null)
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Battery</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($vehicle->battery_level, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                    <div class="h-2 rounded-full {{ $vehicle->battery_level > 20 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ $vehicle->battery_level }}%"></div>
                                </div>
                                @if($vehicle->charging_status)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $vehicle->charging_status === 'charging' ? '⚡ Charging' : 'Not charging' }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        {{-- Odometer --}}
                        @if($vehicle->odometer !== null)
                            <div class="flex items-center justify-between py-3 border-t border-neutral-200 dark:border-neutral-700">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Odometer</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ number_format($vehicle->odometer, 0) }} {{ $vehicle->odometer_unit ?? 'km' }}
                                </span>
                            </div>
                        @endif

                        {{-- Location --}}
                        @if($vehicle->latitude !== null && $vehicle->longitude !== null)
                            <div class="py-3 border-t border-neutral-200 dark:border-neutral-700">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Location</span>
                                    </div>
                                </div>
                                <div id="dashboard-map-{{ $vehicle->id }}" style="height: 150px; border-radius: 0.5rem;" class="overflow-hidden mb-2"></div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        window.initLocationMap('dashboard-map-{{ $vehicle->id }}', {{ $vehicle->latitude }}, {{ $vehicle->longitude }});
                                    });
                                </script>
                                @if($vehicle->location_updated_at)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Updated {{ $vehicle->location_updated_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        {{-- Last Update --}}
                        @if($vehicle->data_updated_at)
                            <div class="pt-3 border-t border-neutral-200 dark:border-neutral-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Last synced {{ $vehicle->data_updated_at->diffForHumans() }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($hasEnodeConnection && count($vehicles) === 0)
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-8 text-center">
            <p class="text-gray-600 dark:text-gray-400">No vehicles found. Try refreshing or reconnecting your account.</p>
        </div>
    @endif
</div>
