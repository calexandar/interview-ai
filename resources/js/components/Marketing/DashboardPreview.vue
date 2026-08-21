<script setup lang="ts">
import { Sparkles } from '@lucide/vue';

const stats = [
    { value: '12', label: 'Active Positions', trend: '+20%' },
    { value: '184', label: 'Candidates', trend: '+12%' },
    { value: '46', label: 'Interviews', trend: '+8%' },
    { value: '18', label: 'Strong Hires', trend: '+28%' },
];

const interviews = [
    {
        initials: 'JD',
        name: 'John Doe',
        role: 'Senior Laravel Developer',
        score: '8.4',
    },
    {
        initials: 'SM',
        name: 'Sarah Miller',
        role: 'Frontend Developer',
        score: '7.8',
    },
    {
        initials: 'RJ',
        name: 'Robert Johnson',
        role: 'Backend Developer',
        score: '9.1',
    },
];
</script>

<template>
    <div class="relative max-w-md">
        <div
            class="rounded-[18px] border border-[#E6E6EF] bg-white p-5 shadow-[0_20px_60px_rgba(23,24,43,0.08)]"
        >
            <div class="mb-4 flex items-center gap-2">
                <div
                    class="flex h-7 w-7 items-center justify-center rounded-md bg-[#F0EBFF]"
                >
                    <Sparkles class="h-4 w-4 text-[#6D4AFF]" />
                </div>
                <span class="text-xs font-medium text-gray-700">Dashboard</span>
            </div>

            <div class="mb-5 grid grid-cols-4 gap-2">
                <div
                    v-for="stat in stats"
                    :key="stat.label"
                    class="rounded-lg bg-gray-50 p-2.5 text-center"
                >
                    <div class="text-base font-bold text-[#17182B]">
                        {{ stat.value }}
                    </div>
                    <div class="mt-0.5 text-[9px] leading-tight text-gray-500">
                        {{ stat.label }}
                    </div>
                    <div
                        class="mt-1 inline-block rounded-full bg-[#EAF8F1] px-1.5 py-0.5 text-[9px] font-medium text-[#22A06B]"
                    >
                        {{ stat.trend }}
                    </div>
                </div>
            </div>

            <div class="mb-2 text-[11px] font-medium text-gray-500">
                Recent Interviews
            </div>
            <div class="space-y-3">
                <div
                    v-for="interview in interviews"
                    :key="interview.initials"
                    class="flex items-center justify-between"
                >
                    <div class="flex min-w-0 items-center gap-2.5">
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#F0EBFF] text-[10px] font-semibold text-[#6D4AFF]"
                        >
                            {{ interview.initials }}
                        </div>
                        <div class="min-w-0">
                            <div
                                class="truncate text-xs font-medium text-[#17182B]"
                            >
                                {{ interview.name }}
                            </div>
                            <div class="truncate text-[10px] text-gray-500">
                                {{ interview.role }}
                            </div>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <span class="text-sm font-semibold text-[#17182B]">
                            {{ interview.score }}
                        </span>
                        <span
                            class="rounded-full bg-[#EAF8F1] px-2 py-0.5 text-[9px] font-medium text-[#22A06B]"
                        >
                            Completed
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
