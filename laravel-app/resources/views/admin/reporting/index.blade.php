<x-admin-layout>

<!-- Page Header -->
<div class="mb-6 flex items-start justify-between">
    <div>
        <h1 class="font-jakarta text-3xl font-extrabold text-[#1C1A17] tracking-tight">Reporting Hub</h1>
        <p class="text-sm text-gray-400 mt-1">Review and manage sanctuary reports</p>
    </div>
</div>

<!-- Load Map Dependencies -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Incident Reports -->
<div class="bg-white rounded-2xl shadow-sm border border-[#E8E2D8] overflow-hidden mt-6">
    <div class="px-6 py-4 border-b border-[#E8E2D8] flex items-center justify-between">
        <div>
            <p class="text-base font-semibold text-gray-800">Incident Reports</p>
            <p class="text-xs text-gray-400 mt-0.5">Review and manage community incident reports</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="px-6 py-3 border-b border-[#F0EBE3] flex items-center gap-3">
        <select id="lfFilter"
                class="text-sm text-gray-600 bg-[#FAF6F0] border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#C9A84C] min-w-[130px]">
            <option value="">All Reports</option>
            <option value="Injury">Injured</option>
            <option value="Missing">Missing</option>
            <option value="Stray">Stray</option>
        </select>
        <input id="lfSearch" type="text" placeholder="Search by description, location..."
               class="flex-1 max-w-sm text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#C9A84C] placeholder-gray-400">
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="lfTable">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Reporter</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Location</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Date Reported</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="lfBody">
                @forelse($userReports as $r)
                    @php
                        $isEmergency = in_array(strtolower($r->type), ['injury', 'injured', 'missing', 'lost']);
                        $isResolved = $r->status === 'Resolved';
                        $reporter   = $r->user?->name ?? $r->reporter_name ?? 'Unknown';
                        $contact    = $r->user?->email ?? $r->reporter_contact ?? '';
                    @endphp
                    <tr class="hover:bg-gray-50 transition lf-row"
                        data-type="{{ $r->type }}"
                        data-search="{{ strtolower($r->description . ' ' . $r->location . ' ' . $reporter) }}">
                        <td class="px-6 py-3.5">
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white {{ $isEmergency ? 'bg-red-500' : 'bg-orange-500' }}">
                                {{ $r->type }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-gray-800 text-sm">{{ Str::title(explode(',', $r->description)[0] ?? 'Unknown') }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($r->description, 35) }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-gray-800 text-sm">{{ $reporter }}</p>
                            <p class="text-xs text-[#C9A84C] mt-0.5">{{ $contact }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-600">{{ $r->location ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-sm text-[#C9A84C] whitespace-nowrap">{{ $r->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3.5">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full border {{ $isResolved ? 'border-gray-300 text-gray-500 bg-gray-50' : 'border-gray-300 text-gray-600 bg-white' }}">
                                {{ $isResolved ? 'Resolved' : 'Active' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.reports.show', $r) }}"
                                   class="text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">
                                    View Details
                                </a>
                                @if(!$isResolved)
                                    <form method="POST" action="{{ route('admin.reports.status', $r) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="Resolved">
                                        <button type="submit"
                                                class="text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">
                                            Mark Resolved
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400 italic font-cabinet">
                            No lost or found reports yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Live Map Tracker -->
<div class="mt-6 mb-8 bg-white rounded-[32px] shadow-sm border border-amber-100 overflow-hidden" x-data="reportingMap()">
    <div class="px-8 py-5 border-b border-amber-50 flex items-center justify-between bg-[#FAF6F0]/30">
        <div>
            <h3 class="text-xl font-serif font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Sanctuary Live Tracker
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">Unified GPS tracking for all resident cats</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="locateMe" class="px-4 py-2 bg-amber-50 text-amber-700 font-bold text-xs rounded-xl hover:bg-amber-100 transition shadow-sm flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                My Location
            </button>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row" style="height: 550px;">
        <!-- Interactive Map -->
        <div class="flex-1 relative border-r border-amber-50">
            <div id="reporting-map" class="w-full h-full z-0"></div>
            
            <!-- Legend (Floating) -->
            <div class="absolute bottom-6 left-6 z-[999] bg-white/90 backdrop-blur-md border border-amber-100 rounded-2xl p-5 shadow-xl text-[11px] space-y-2 max-h-[220px] overflow-y-auto">
                <p class="font-bold text-gray-400 uppercase tracking-widest text-[9px] mb-1.5">Status Key</p>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500 shadow-sm border-2 border-white"></span><span class="font-semibold text-gray-700">Available</span></div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-orange-500 shadow-sm border-2 border-white"></span><span class="font-semibold text-gray-700">Pending</span></div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500 shadow-sm border-2 border-white"></span><span class="font-semibold text-gray-700">Adopted</span></div>
                
                <p class="font-bold text-gray-400 uppercase tracking-widest text-[9px] pt-1.5 border-t border-amber-50 mb-1.5">Incidents Key</p>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded-full bg-[#ef4444] border-2 border-white text-white font-black flex items-center justify-center text-[8px] leading-none">!</span><span class="font-semibold text-gray-700">Injury</span></div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded-full bg-[#f97316] border-2 border-white text-white font-black flex items-center justify-center text-[8px] leading-none">!</span><span class="font-semibold text-gray-700">Missing</span></div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded-full bg-[#3b82f6] border-2 border-white text-white font-black flex items-center justify-center text-[8px] leading-none">!</span><span class="font-semibold text-gray-700">Stray</span></div>
            </div>
        </div>
        <!-- Sidebar -->
        <div class="w-full lg:w-80 flex flex-col bg-white border-l border-amber-50">
            <div class="p-6 border-b border-amber-50 bg-[#FAF6F0]/20">
                <h3 class="font-serif font-bold text-lg text-amber-900 mb-3">Cat Directory</h3>
                <input type="text" x-model="search" placeholder="Search by name..." 
                       class="w-full text-sm bg-white border border-amber-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none placeholder-gray-400 shadow-sm transition-all">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-4">Showing <span x-text="filteredCats.length"></span> active trackers</p>
            </div>
            
            <div class="flex-1 overflow-y-auto divide-y divide-gray-50 custom-scrollbar">
                <template x-for="cat in filteredCats" :key="cat.id">
                    <div @click="focusCat(cat)" class="flex items-center gap-4 px-6 py-5 hover:bg-amber-50/50 cursor-pointer transition-all group border-l-4 border-transparent hover:border-amber-500">
                        <div class="relative flex-shrink-0">
                            <img :src="cat.image_url" class="w-14 h-14 rounded-2xl object-cover shadow-md ring-2 ring-white group-hover:ring-amber-200 transition-all duration-300">
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white shadow-sm" :class="statusColor(cat.status)"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-0.5">
                                <h4 class="text-lg font-serif font-bold text-gray-800 truncate group-hover:text-amber-700 transition-colors" x-text="cat.name"></h4>
                                <template x-if="cat.gps_live">
                                    <span class="inline-block px-1.5 py-0.5 bg-red-100 text-red-600 text-[8px] font-bold uppercase rounded-full animate-pulse">🔴 LIVE GPS</span>
                                </template>
                            </div>
                            <p class="text-xs text-gray-500 truncate mb-2" x-text="cat.breed"></p>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" x-text="formatDate(cat.updated_at)"></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="filteredCats.length === 0" class="p-10 text-center text-gray-400 italic font-serif">
                    <p class="text-sm">No active trackers found.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #D1D5DB; }
</style>

<script>
function reportingMap() {
    return {
        map: null,
        cats: @json($catsWithGps).map(c => ({
            ...c,
            image_url: c.image ? `/storage/${c.image}` : `https://ui-avatars.com/api/?name=${c.name}&background=fde68a&color=92400e`
        })),
        reports: @json($userReports).map(r => {
            let lat = null, lng = null;
            if (r.location) {
                let parts = r.location.split(',');
                if (parts.length === 2) {
                    lat = parseFloat(parts[0].trim());
                    lng = parseFloat(parts[1].trim());
                }
            }
            return {
                ...r,
                lat: lat,
                lng: lng,
                reporter_name: r.user ? r.user.name : (r.reporter_name || 'Unknown')
            };
        }).filter(r => r.lat !== null && r.lng !== null),
        search: '',
        markers: [],
        reportMarkers: {},

        init() {
            this.initMap();
            window.focusReport = (id, lat, lng) => {
                this.focusReportLocation(id, lat, lng);
            };
        },

        get filteredCats() {
            if (!this.search) return this.cats;
            return this.cats.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()));
        },

        initMap() {
            const firstCat = this.cats[0];
            const center = firstCat ? [firstCat.gps_lat, firstCat.gps_lng] : [3.2535, 101.7323];
            
            this.map = L.map('reporting-map', { zoomControl: false }).setView(center, 15);
            L.control.zoom({ position: 'bottomright' }).addTo(this.map);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);

            this.updateMarkers();
            this.updateReportMarkers();
        },

        updateMarkers() {
            this.markers.forEach(m => this.map.removeLayer(m));
            this.markers = [];

            this.cats.forEach(cat => {
                let colorName = 'green';
                if (cat.status === 'Pending') colorName = 'orange'; 
                if (cat.status === 'Adopted') colorName = 'blue';
                if (!['Available', 'Pending', 'Adopted'].includes(cat.status)) colorName = 'red';

                const colorCode = this.hexColor(colorName);
                
                const icon = L.divIcon({
                    className: 'custom-pin',
                    html: `<div style="background-color:${colorCode}; width:20px; height:20px; border-radius:50%; border:3px solid white; box-shadow:0 2px 10px rgba(0,0,0,0.2);"></div>`,
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });

                const marker = L.marker([cat.gps_lat, cat.gps_lng], { icon }).addTo(this.map);
                
                const popupContent = `
                    <div class="text-center p-3 min-w-[160px]">
                        <div class="w-16 h-16 rounded-full overflow-hidden mx-auto mb-3 border-2 shadow-sm" style="border-color: ${colorCode}">
                            <img src="${cat.image_url}" class="w-full h-full object-cover">
                        </div>
                        <h3 class="font-serif font-bold text-lg text-gray-800 leading-tight mb-1">${cat.name}</h3>
                        ${cat.gps_live ? '<span class="inline-block px-2 py-0.5 bg-red-100 text-red-600 text-[9px] font-bold uppercase rounded-full mb-2 animate-pulse">🔴 LIVE GPS</span>' : ''}
                        <div class="mb-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide text-white" style="background-color: ${colorCode}">${cat.status || 'Available'}</span>
                        </div>
                        ${cat.gps_battery ? `<div class="text-xs text-gray-600 mt-1 font-bold">🔋 Battery: ${cat.gps_battery}%</div>` : ''}
                        <div class="text-[10px] text-gray-500 mt-1">Last seen: ${this.formatDate(cat.updated_at)}</div>
                        <div class="mt-3">
                            <a href="/admin/cats/${cat.id}" class="inline-block w-full py-1.5 bg-gray-800 hover:bg-amber-600 text-white text-[10px] font-bold rounded-lg transition-colors">View Details</a>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent, { closeButton: false, className: 'rounded-xl shadow-2xl overflow-hidden' });
                this.markers.push(marker);
            });
        },

        updateReportMarkers() {
            this.reports.forEach(report => {
                let colorCode = '#ef4444'; // default red (injury)
                if (report.type === 'Missing') colorCode = '#f97316'; // orange
                if (report.type === 'Stray') colorCode = '#3b82f6'; // blue
                
                const icon = L.divIcon({
                    className: 'report-pin',
                    html: `<div style="background-color:${colorCode}; width:22px; height:22px; border-radius:50%; border:3px solid white; box-shadow:0 2px 8px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; color:white; font-family:sans-serif; font-size:10px; font-weight:900;" class="flex items-center justify-center font-bold">!</div>`,
                    iconSize: [22, 22],
                    iconAnchor: [11, 11]
                });

                const marker = L.marker([report.lat, report.lng], { icon }).addTo(this.map);
                
                const popupContent = `
                    <div class="text-center p-3 min-w-[170px]">
                        <div class="mb-2">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider text-white" style="background-color: ${colorCode}">
                                🚨 ${report.type}
                            </span>
                        </div>
                        <h3 class="font-serif font-bold text-sm text-gray-800 leading-tight mb-1">Report #${report.id}</h3>
                        <p class="text-xs text-gray-600 mb-2 font-medium italic">"${report.description.substring(0, 45)}${report.description.length > 45 ? '...' : ''}"</p>
                        <div class="text-[10px] text-gray-500">Reporter: ${report.reporter_name}</div>
                        <div class="text-[9px] text-gray-400 mt-0.5">Status: <span class="font-bold text-gray-600">${report.status}</span></div>
                        <div class="mt-3">
                            <a href="/admin/reports/${report.id}" class="inline-block w-full py-1.5 bg-[#C9A84C] hover:bg-amber-600 text-white text-[10px] font-bold rounded-lg transition-colors shadow-sm">View Details</a>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent, { closeButton: false, className: 'rounded-xl shadow-2xl overflow-hidden' });
                this.reportMarkers[report.id] = marker;
            });
        },

        focusCat(cat) {
            this.map.flyTo([cat.gps_lat, cat.gps_lng], 17, { duration: 1.5 });
            const marker = this.markers.find(m => m.getLatLng().lat == cat.gps_lat && m.getLatLng().lng == cat.gps_lng);
            if (marker) marker.openPopup();
        },

        focusReportLocation(id, lat, lng) {
            const mapContainer = document.getElementById('reporting-map');
            if (mapContainer) {
                mapContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            setTimeout(() => {
                this.map.flyTo([lat, lng], 17, { duration: 1.2 });
                const marker = this.reportMarkers[id];
                if (marker) {
                    setTimeout(() => {
                        marker.openPopup();
                    }, 1200);
                }
            }, 300);
        },

        locateMe() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    const { latitude, longitude } = pos.coords;
                    this.map.flyTo([latitude, longitude], 15);
                    L.marker([latitude, longitude]).addTo(this.map).bindPopup("You are here").openPopup();
                });
            }
        },

        statusColor(status) {
            if (status === 'Pending') return 'bg-orange-500';
            if (status === 'Adopted') return 'bg-blue-500';
            if (!['Available', 'Pending', 'Adopted'].includes(status)) return 'bg-red-500';
            return 'bg-green-500';
        },

        hexColor(name) {
            const colors = {
                'green': '#22c55e',
                'orange': '#f97316',
                'blue': '#3b82f6',
                'red': '#ef4444'
            };
            return colors[name] || '#9ca3af';
        },

        formatDate(dateString) {
            if (!dateString) return 'Unknown';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' +
                date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
        }
    }
}

// ---------- Lost & Found filter ----------
document.addEventListener('DOMContentLoaded', () => {
    const lfFilter = document.getElementById('lfFilter');
    const lfSearch = document.getElementById('lfSearch');
    
    if (lfFilter && lfSearch) {
        const filterFn = () => {
            const type   = lfFilter.value.toLowerCase();
            const search = lfSearch.value.toLowerCase();
            document.querySelectorAll('.lf-row').forEach(row => {
                const matchType   = !type   || row.dataset.type.toLowerCase() === type;
                const matchSearch = !search || row.dataset.search.includes(search);
                row.style.display = matchType && matchSearch ? '' : 'none';
            });
        };
        lfFilter.addEventListener('change', filterFn);
        lfSearch.addEventListener('input', filterFn);
    }
});
</script>

</x-admin-layout>
