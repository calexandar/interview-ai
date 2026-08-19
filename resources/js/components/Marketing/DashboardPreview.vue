<template>
    <div class="relative max-w-md">
        <div class="rounded-2xl border border-[#E8E8EF] bg-white p-5 shadow-sm">
            <!-- Header -->
            <div class="mb-4 flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-md bg-purple-100">
                    <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-700">Dashboard</span>
            </div>

            <!-- Stats -->
            <div class="mb-4 grid grid-cols-3 gap-3">
                <div class="rounded-lg bg-gray-50 p-3 text-center">
                    <div class="text-lg font-bold text-[#17171C]">128</div>
                    <div class="text-[10px] text-gray-500">Interviews</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-3 text-center">
                    <div class="text-lg font-bold text-[#17171C]">342</div>
                    <div class="text-[10px] text-gray-500">Candidates</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-3 text-center">
                    <div class="text-lg font-bold text-[#17171C]">56</div>
                    <div class="text-[10px] text-gray-500">Hires</div>
                </div>
            </div>

            <!-- Recent Interviews -->
            <div>
                <div class="mb-2 text-[11px] font-medium text-gray-500">Recent Interviews</div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-purple-500" />
                            <span class="text-[11px] text-gray-600">Candidate</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-1.5 w-20 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full w-[85%] rounded-full bg-purple-500" />
                            </div>
                            <span class="text-[11px] font-medium text-gray-700">8.5</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-blue-500" />
                            <span class="text-[11px] text-gray-600">Candidate</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-1.5 w-20 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full w-[78%] rounded-full bg-blue-500" />
                            </div>
                            <span class="text-[11px] font-medium text-gray-700">7.8</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-green-500" />
                            <span class="text-[11px] text-gray-600">Candidate</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-1.5 w-20 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full w-[91%] rounded-full bg-green-500" />
                            </div>
                            <span class="text-[11px] font-medium text-gray-700">9.1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
