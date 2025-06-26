<x-layouts.app :title="__('Branch Map View')">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <!-- Custom Map Styles -->
    <style>
        .custom-marker {
            background: transparent !important;
            border: none !important;
        }
        .leaflet-popup-content-wrapper {
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }
        .leaflet-popup-tip {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15) !important;
        }
        .leaflet-control-zoom a {
            border-radius: 4px !important;
        }
        .leaflet-popup-content {
            margin: 0 !important;
        }
        
        /* Full screen map container */
        #branchMap {
            height: calc(100vh - 120px) !important;
            width: 100% !important;
        }
        
        /* Floating controls */
        .map-controls {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            z-index: 1000;
            pointer-events: none;
        }
        
        .map-controls > * {
            pointer-events: auto;
        }
        
        .floating-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .dark .floating-panel {
            background: rgba(39, 39, 42, 0.95);
            border: 1px solid rgba(82, 82, 91, 0.2);
        }

        .branch-list-panel {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 300px;
            max-height: calc(100vh - 160px);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .stats-panel {
            position: absolute;
            bottom: 20px;
            left: 20px;
            width: 280px;
            z-index: 1000;
        }
        
        .filter-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 280px;
            z-index: 1000;
        }
        
        /* Toggle buttons for panels */
        .panel-toggle {
            position: absolute;
            z-index: 1001;
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .panel-toggle:hover {
            transform: scale(1.05);
        }
        
        .toggle-filters {
            top: 80px;
            left: 20px;
        }
        
        .toggle-branches {
            top: 80px;
            right: 20px;
        }
        
        .toggle-stats {
            bottom: 80px;
            left: 20px;
        }
    </style>

    <div class="relative min-h-screen bg-zinc-50 dark:bg-zinc-900 overflow-hidden">
        <!-- Header Bar -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 relative z-50">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('branches.index')" wire:navigate>
                            {{ __('Back') }}
                        </flux:button>
                        <div>
                            <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                                {{ __('Branch Map View') }}
                            </h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">
                            {{ $stats['mapped_branches'] }} {{ __('of') }} {{ $stats['total_branches'] }} {{ __('branches mapped') }}
                        </span>
                        
                        <!-- Legend -->
                        <div class="hidden sm:flex items-center space-x-4 ml-4">
                            <div class="flex items-center space-x-2 text-sm">
                                <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('High') }}</span>
                            </div>
                            <div class="flex items-center space-x-2 text-sm">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Good') }}</span>
                            </div>
                            <div class="flex items-center space-x-2 text-sm">
                                <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Average') }}</span>
                            </div>
                            <div class="flex items-center space-x-2 text-sm">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Poor') }}</span>
                            </div>
                        </div>

                        @if(auth()->user()->hasRole('admin'))
                            <flux:button variant="outline" size="sm" icon="plus" :href="route('branches.create')" wire:navigate>
                                {{ __('Add Branch') }}
                            </flux:button>
                        @endif
                        <flux:button variant="outline" size="sm" icon="bug-ant" onclick="window.open('{{ route('branches.map') }}?debug=static', '_blank')">
                            {{ __('Debug') }}
                        </flux:button>
                        <flux:button variant="primary" size="sm" icon="chart-bar" :href="route('reports.operational', ['type' => 'branch_performance'])" wire:navigate>
                            {{ __('Reports') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Map Container -->
        <div class="relative">
            <div id="branchMap"></div>
            
            <!-- Loading indicator -->
            <div id="mapLoading" class="absolute inset-0 flex items-center justify-center bg-zinc-100 dark:bg-zinc-700 z-10">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Loading map...') }}</p>
                </div>
            </div>
            
            <!-- Error indicator -->
            <div id="mapError" class="hidden absolute inset-0 flex items-center justify-center bg-red-50 dark:bg-red-900/20 z-10">
                <div class="text-center">
                    <flux:icon.exclamation-triangle class="w-8 h-8 text-red-600 mx-auto mb-2" />
                    <p class="text-sm text-red-600 dark:text-red-400">{{ __('Failed to load map. Please refresh the page.') }}</p>
                </div>
            </div>

            <!-- Panel Toggle Buttons -->
            <button class="panel-toggle toggle-filters floating-panel" onclick="togglePanel('filter-panel')">
                <flux:icon.funnel class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
            </button>
            
            <button class="panel-toggle toggle-branches floating-panel" onclick="togglePanel('branch-list-panel')">
                <flux:icon.building-office-2 class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
            </button>
            
            <button class="panel-toggle toggle-stats floating-panel" onclick="togglePanel('stats-panel')">
                <flux:icon.chart-bar class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
            </button>

            <!-- Floating Filter Panel -->
            <div id="filter-panel" class="filter-panel floating-panel p-4 hidden">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Filters') }}</h3>
                    <button onclick="togglePanel('filter-panel')" class="text-zinc-400 hover:text-zinc-600">
                        <flux:icon.x-mark class="w-4 h-4" />
                    </button>
                </div>
                
                <form method="GET" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">{{ __('Search') }}</label>
                        <input type="text" name="search" placeholder="{{ __('Search branches...') }}" 
                               value="{{ $search }}" 
                               class="w-full px-2 py-1 text-sm border border-zinc-300 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">{{ __('Status') }}</label>
                        <select name="status" class="w-full px-2 py-1 text-sm border border-zinc-300 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            <option value="under_maintenance" {{ $status === 'under_maintenance' ? 'selected' : '' }}>{{ __('Under Maintenance') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">{{ __('City') }}</label>
                        <select name="city" class="w-full px-2 py-1 text-sm border border-zinc-300 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                            <option value="">{{ __('All Cities') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ $city === request('city') ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex space-x-2 pt-2">
                        <button type="submit" class="flex-1 px-3 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            {{ __('Apply') }}
                        </button>
                        <button type="button" onclick="window.location.href='{{ route('branches.map') }}'" 
                                class="px-3 py-1 text-xs bg-zinc-500 text-white rounded-md hover:bg-zinc-600">
                            {{ __('Clear') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Floating Branch List Panel -->
            <div id="branch-list-panel" class="branch-list-panel floating-panel hidden">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Branches') }} ({{ $branchesWithAnalytics->count() }})
                        </h3>
                        <button onclick="togglePanel('branch-list-panel')" class="text-zinc-400 hover:text-zinc-600">
                            <flux:icon.x-mark class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($branchesWithAnalytics as $data)
                        @php $branch = $data['branch'] @endphp
                        <div class="p-3 hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer branch-list-item" data-branch-id="{{ $branch->id }}">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center
                                    @if($data['performance_score'] >= 80) bg-emerald-500
                                    @elseif($data['performance_score'] >= 60) bg-blue-500
                                    @elseif($data['performance_score'] >= 40) bg-amber-500
                                    @else bg-red-500
                                    @endif">
                                    <flux:icon.building-office-2 class="w-3 h-3 text-white" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $branch->name }}</h4>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $branch->city }}</p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                            {{ number_format($data['total_members']) }}
                                        </span>
                                        <span class="text-xs text-zinc-400">•</span>
                                        <span class="text-xs font-medium 
                                            @if($data['performance_score'] >= 80) text-emerald-600 dark:text-emerald-400
                                            @elseif($data['performance_score'] >= 60) text-blue-600 dark:text-blue-400
                                            @elseif($data['performance_score'] >= 40) text-amber-600 dark:text-amber-400
                                            @else text-red-600 dark:text-red-400
                                            @endif">
                                            {{ number_format($data['performance_score']) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center">
                            <flux:icon.map class="w-6 h-6 text-zinc-400 dark:text-zinc-600 mx-auto mb-2" />
                            <p class="text-zinc-500 dark:text-zinc-400 text-xs">{{ __('No branches with coordinates found') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Floating Statistics Panel -->
            <div id="stats-panel" class="stats-panel floating-panel p-4 hidden">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Network Stats') }}</h3>
                    <button onclick="togglePanel('stats-panel')" class="text-zinc-400 hover:text-zinc-600">
                        <flux:icon.x-mark class="w-4 h-4" />
                    </button>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Total Branches') }}</span>
                        <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['total_branches'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Mapped Branches') }}</span>
                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $stats['mapped_branches'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Active Branches') }}</span>
                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $stats['active_branches'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Total Members') }}</span>
                        <span class="text-sm font-semibold text-purple-600 dark:text-purple-400">{{ number_format($stats['total_members']) }}</span>
                    </div>
                    @if($stats['top_performer'])
                        <div class="pt-2 border-t border-zinc-200 dark:border-zinc-700">
                            <span class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Top Performer') }}</span>
                            <p class="text-sm font-semibold text-amber-600 dark:text-amber-400">{{ $stats['top_performer']->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Global variables for debugging
        window.mapInstance = null;
        window.markersObj = {};
        window.branchData = @json($branchesWithAnalytics);

        function togglePanel(panelId) {
            const panel = document.getElementById(panelId);
            if (panel.classList.contains('hidden')) {
                // Hide all other panels first
                ['filter-panel', 'branch-list-panel', 'stats-panel'].forEach(id => {
                    if (id !== panelId) {
                        document.getElementById(id).classList.add('hidden');
                    }
                });
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        }

        function initializeMap() {
            try {
                console.log('Initializing full-screen map...');
                console.log('Branch data:', window.branchData);

                // Hide loading indicator
                document.getElementById('mapLoading').style.display = 'none';

                // Map center coordinates
                const mapCenter = [{{ $mapCenter['lat'] }}, {{ $mapCenter['lng'] }}];
                console.log('Map center:', mapCenter);

                // Initialize map
                window.mapInstance = L.map('branchMap').setView(mapCenter, 7);

                // Add tile layer with error handling
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 18,
                    errorTileUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0ic2Fucy1zZXJpZiIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk1hcCB1bmF2YWlsYWJsZTwvdGV4dD48L3N2Zz4='
                }).addTo(window.mapInstance);

                console.log('Tile layer added');

                // Add markers
                let markerCount = 0;
                window.branchData.forEach((data, index) => {
                    try {
                        const branch = data.branch;
                        console.log(`Processing branch ${index}:`, branch.name, branch.coordinates);
                        
                        if (branch.coordinates && branch.coordinates.latitude && branch.coordinates.longitude) {
                            const lat = parseFloat(branch.coordinates.latitude);
                            const lng = parseFloat(branch.coordinates.longitude);
                            
                            console.log(`Parsed coordinates: lat=${lat}, lng=${lng}`);
                            
                            if (!isNaN(lat) && !isNaN(lng)) {
                                // Create simple circle marker instead of custom icon
                                const color = getPerformanceColor(data.performance_score, branch.status);
                                
                                const marker = L.circleMarker([lat, lng], {
                                    radius: 12,
                                    fillColor: color,
                                    color: '#ffffff',
                                    weight: 3,
                                    opacity: 1,
                                    fillOpacity: 0.9
                                }).addTo(window.mapInstance);

                                // Simple popup content
                                const popupContent = `
                                    <div style="min-width: 200px;">
                                        <h3 style="margin: 0 0 8px 0; font-weight: bold;">${branch.name}</h3>
                                        <p style="margin: 4px 0;"><strong>Code:</strong> ${branch.code}</p>
                                        <p style="margin: 4px 0;"><strong>City:</strong> ${branch.city}</p>
                                        <p style="margin: 4px 0;"><strong>Status:</strong> ${branch.status}</p>
                                        <p style="margin: 4px 0;"><strong>Members:</strong> ${data.total_members.toLocaleString()}</p>
                                        <p style="margin: 4px 0;"><strong>Performance:</strong> ${Math.round(data.performance_score)}%</p>
                                        <hr style="margin: 8px 0;">
                                        <div style="text-align: center;">
                                            <a href="/branches/${branch.id}" style="display: inline-block; padding: 4px 8px; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px; margin-right: 4px;">View</a>
                                            <a href="/branches/${branch.id}/edit" style="display: inline-block; padding: 4px 8px; background: #6b7280; color: white; text-decoration: none; border-radius: 4px;">Edit</a>
                                        </div>
                                    </div>
                                `;
                                
                                marker.bindPopup(popupContent);
                                window.markersObj[branch.id] = marker;
                                markerCount++;
                                
                                console.log(`Added marker ${markerCount} for ${branch.name}`);
                            } else {
                                console.warn(`Invalid coordinates for ${branch.name}: lat=${lat}, lng=${lng}`);
                            }
                        } else {
                            console.warn(`No coordinates for ${branch.name}`);
                        }
                    } catch (err) {
                        console.error(`Error processing branch ${index}:`, err);
                    }
                });

                console.log(`Total markers added: ${markerCount}`);

                // Fit map to markers if we have any
                if (markerCount > 0) {
                    try {
                        const group = new L.featureGroup(Object.values(window.markersObj));
                        window.mapInstance.fitBounds(group.getBounds().pad(0.1));
                        console.log('Map bounds fitted to markers');
                    } catch (err) {
                        console.error('Error fitting bounds:', err);
                    }
                }

                // Add click handlers for branch list
                document.querySelectorAll('.branch-list-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const branchId = this.getAttribute('data-branch-id');
                        const marker = window.markersObj[branchId];
                        
                        if (marker) {
                            window.mapInstance.setView(marker.getLatLng(), 12);
                            marker.openPopup();
                            
                            // Highlight the list item
                            document.querySelectorAll('.branch-list-item').forEach(i => i.classList.remove('bg-blue-50', 'dark:bg-blue-900/20'));
                            this.classList.add('bg-blue-50', 'dark:bg-blue-900/20');
                        }
                    });
                });

                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.mapInstance) {
                        window.mapInstance.invalidateSize();
                    }
                });

                console.log('Full-screen map initialization complete');

            } catch (error) {
                console.error('Map initialization error:', error);
                document.getElementById('mapLoading').style.display = 'none';
                document.getElementById('mapError').style.display = 'flex';
            }
        }

        function getPerformanceColor(score, status) {
            if (status !== 'active') return '#6b7280'; // Gray for inactive
            
            if (score >= 80) return '#10b981'; // Emerald for high performance
            if (score >= 60) return '#3b82f6'; // Blue for good performance  
            if (score >= 40) return '#f59e0b'; // Amber for average performance
            return '#ef4444'; // Red for poor performance
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeMap);
        } else {
            initializeMap();
        }

        // Add a manual initialization button for debugging
        window.reinitMap = function() {
            if (window.mapInstance) {
                window.mapInstance.remove();
                window.mapInstance = null;
                window.markersObj = {};
            }
            document.getElementById('mapLoading').style.display = 'flex';
            document.getElementById('mapError').style.display = 'none';
            setTimeout(initializeMap, 100);
        };

        // Close panels when clicking on map
        document.addEventListener('click', function(e) {
            if (e.target.closest('.floating-panel') || e.target.closest('.panel-toggle')) {
                return;
            }
            
            // Close all panels when clicking elsewhere
            ['filter-panel', 'branch-list-panel', 'stats-panel'].forEach(id => {
                document.getElementById(id).classList.add('hidden');
            });
        });
    </script>
</x-layouts.app>