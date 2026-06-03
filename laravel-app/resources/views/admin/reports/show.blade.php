<x-admin-layout>
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Top Navigation & Actions -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide uppercase 
                        {{ $report->status === 'Resolved' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 
                           ($report->status === 'Closed' ? 'bg-gray-100 text-gray-800 border border-gray-200' : 'bg-amber-100 text-amber-800 border border-amber-200') }}">
                        {{ $report->status }}
                    </span>
                    <span class="text-xs text-gray-500 font-medium">Report #{{ $report->id }}</span>
                </div>
                <h2 class="font-jakarta text-3xl font-extrabold text-[#1C1A17] tracking-tight">
                    Incident Detail
                </h2>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-[#E8E2D8] hover:bg-[#FAF8F5] text-gray-700 font-semibold rounded-xl text-sm transition-all shadow-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Panel: Incident Details & Media (2 columns on lg) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Card 1: Details -->
                <div class="bg-white rounded-3xl border border-[#E8E2D8] shadow-sm p-6 sm:p-8">
                    <!-- Header with Type -->
                    <div class="flex items-start justify-between border-b border-[#F2EDE3] pb-5 mb-6">
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Incident Type</span>
                            <div class="flex items-center gap-2 mt-1">
                                <!-- Type Icon & Badge -->
                                @php
                                    $typeColors = [
                                        'Injury' => 'bg-red-50 text-red-700 border-red-200',
                                        'Missing' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Stray' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    ];
                                    $typeColor = $typeColors[$report->type] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                @endphp
                                <span class="px-3 py-1 rounded-xl text-sm font-bold border {{ $typeColor }} flex items-center gap-1.5">
                                    @if($report->type === 'Injury')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    @elseif($report->type === 'Missing')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918"/>
                                        </svg>
                                    @endif
                                    {{ $report->type }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Date Submitted</span>
                            <p class="text-sm font-bold text-gray-800 mt-1">
                                {{ $report->created_at->format('d M Y') }}
                                <span class="block text-xs font-medium text-gray-500 mt-0.5">{{ $report->created_at->format('h:i A') }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-2">Description</span>
                        <div class="bg-[#FAF8F5] border border-[#F0EBE3] rounded-2xl p-5 text-gray-700 leading-relaxed text-base font-medium relative overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#C9A84C]"></div>
                            {{ $report->description ?? 'No description provided.' }}
                        </div>
                    </div>

                    <!-- Location details if provided -->
                    @if($report->location)
                        <div class="border-t border-[#F2EDE3] pt-6">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-2">Location Details</span>
                            <div class="bg-[#FAF8F5] border border-[#F0EBE3] rounded-2xl p-4 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25s-7.5-4.108-7.5-11.25A7.5 7.5 0 1119.5 10.5z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $report->location }}</p>
                                    <p class="text-xs text-gray-500">Provided by reporter</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Card 2: Attached Photo if present -->
                @if($report->photo_path)
                    <div class="bg-white rounded-3xl border border-[#E8E2D8] shadow-sm p-6 sm:p-8">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-4">Attached Photo</span>
                        <div class="relative rounded-2xl overflow-hidden border border-[#FAF8F5] bg-gray-50 group max-h-[500px]">
                            <img src="{{ Storage::url($report->photo_path) }}" alt="Incident Photo" 
                                 class="w-full object-contain mx-auto max-h-[480px] transition-transform duration-300 group-hover:scale-[1.01]">
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Panel: Sidebar & Admin Controls (1 column) -->
            <div class="space-y-8">
                <!-- Card 3: Administration Controls (Status) -->
                <div class="bg-white rounded-3xl border border-[#E8E2D8] shadow-sm p-6 sm:p-8">
                    <h3 class="font-jakarta text-lg font-bold text-[#1C1A17] mb-5 border-b border-[#F2EDE3] pb-3">
                        Status Management
                    </h3>
                    
                    <form action="{{ route('admin.reports.status', $report) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Update Status</label>
                            <div class="relative">
                                <select name="status" onchange="this.form.submit()"
                                    class="w-full pl-4 pr-10 py-3 rounded-xl border border-[#E8E2D8] bg-white text-gray-800 font-bold focus:border-[#C9A84C] focus:ring-1 focus:ring-[#C9A84C] transition appearance-none cursor-pointer">
                                    <option value="Pending" {{ $report->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Resolved" {{ $report->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="Closed" {{ $report->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Status Notice Email -->
                        <div class="text-[11px] text-gray-400 flex gap-2 items-start mt-2">
                            <svg class="w-4 h-4 text-[#C9A84C] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.083 1.083l-.041.02H11.25zm1.5 1.5H12v.008h.008v-.008zM12 18a6 6 0 100-12 6 6 0 000 12z"/>
                            </svg>
                            <span>Changing status automatically notifies the reporter via email notifications.</span>
                        </div>
                    </form>

                    <!-- Spacers & Delete Action -->
                    <div class="border-t border-[#F2EDE3] pt-6 mt-6 flex justify-stretch">
                        <form id="form-del-report-{{ $report->id }}" action="{{ route('admin.reports.destroy', $report) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                    onclick="showConfirmModal({ title: 'Delete Report?', message: 'Are you sure you want to permanently delete this incident report?', formId: 'form-del-report-{{ $report->id }}' })"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 font-bold rounded-xl text-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Incident Report
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Card 4: Reporter Card -->
                <div class="bg-white rounded-3xl border border-[#E8E2D8] shadow-sm p-6 sm:p-8">
                    <h3 class="font-jakarta text-lg font-bold text-[#1C1A17] mb-5 border-b border-[#F2EDE3] pb-3">
                        Reporter Information
                    </h3>

                    @php
                        $reporterName = $report->reporter_name ?? ($report->user?->name ?? 'Anonymous');
                        $reporterContact = $report->reporter_contact ?? 'No contact provided';
                        $reporterEmail = $report->user?->email ?? null;
                    @endphp

                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-[#C9A84C]/10 border border-[#C9A84C]/20 text-[#C9A84C] flex items-center justify-center font-jakarta font-black text-lg">
                            {{ strtoupper(substr($reporterName, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-gray-800 truncate text-base leading-tight">{{ $reporterName }}</h4>
                            <p class="text-xs text-[#C9A84C] font-semibold mt-0.5">
                                {{ $report->user ? 'Registered Member' : 'Guest Submitter' }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4 text-sm font-medium">
                        <!-- Contact number -->
                        <div>
                            <span class="text-xs text-gray-400 font-semibold block mb-1">Phone Number</span>
                            <div class="flex items-center gap-2 text-gray-700">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.802-5.118-4.096-6.922-6.922l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                                </svg>
                                <span>{{ $reporterContact }}</span>
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <span class="text-xs text-gray-400 font-semibold block mb-1">Email Address</span>
                            <div class="flex items-center gap-2 text-gray-700">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                </svg>
                                <span>{{ $reporterEmail ?? 'No email associated' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Reporter communication actions -->
                    @if($reporterEmail || ($reporterContact && $reporterContact !== 'No contact provided'))
                        <div class="border-t border-[#F2EDE3] pt-5 mt-5 flex flex-col gap-2.5">
                            @if($reporterEmail)
                                <a href="mailto:{{ $reporterEmail }}?subject=Update%20regarding%20incident%20report%20%23{{ $report->id }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-[#FAF8F5] border border-[#E8E2D8] text-gray-700 font-semibold rounded-xl text-sm transition-all shadow-sm">
                                    Send Email
                                </a>
                            @endif
                            @if($reporterContact && $reporterContact !== 'No contact provided')
                                <a href="tel:{{ $reporterContact }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#C9A84C] hover:bg-[#b8963e] text-white font-bold rounded-xl text-sm transition-all">
                                    Call Contact
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
