<script setup lang="ts">
import { Check } from '@lucide/vue';
import { ref } from 'vue';
import { Skeleton } from '@/components/ui/skeleton';
import type { InterviewSection, SectionStatus } from '@/types/interview';

withDefaults(
    defineProps<{
        jobTitle: string;
        percentage: number;
        sections: InterviewSection[];
        loading?: boolean;
    }>(),
    {
        loading: false,
    },
);

type TabId = 'progress' | 'notes';

const activeTab = ref<TabId>('progress');
const isExpandedMobile = ref(false);
const notes = ref('');

const tabs = [
    { id: 'progress' as const, label: 'Interview Progress' },
    { id: 'notes' as const, label: 'Notes' },
];

function selectTab(tab: TabId): void {
    activeTab.value = tab;
}

function statusLabel(status: SectionStatus): string {
    switch (status) {
        case 'completed':
            return 'Completed';
        case 'in_progress':
            return 'In Progress';
        default:
            return 'Pending';
    }
}

function statusTextClass(status: SectionStatus): string {
    switch (status) {
        case 'completed':
            return 'text-success';
        case 'in_progress':
            return 'text-primary';
        default:
            return 'text-muted-foreground';
    }
}

function statusBadgeClass(status: SectionStatus): string {
    switch (status) {
        case 'completed':
            return 'bg-success/10 text-success';
        case 'in_progress':
            return 'bg-primary/10 text-primary';
        default:
            return 'bg-muted text-muted-foreground';
    }
}

function dotClass(status: SectionStatus): string {
    switch (status) {
        case 'completed':
            return 'bg-success text-success-foreground';
        case 'in_progress':
            return 'bg-primary text-primary-foreground ring-4 ring-primary/15';
        default:
            return 'border border-border bg-card';
    }
}
</script>

<template>
    <aside
        class="flex h-full min-h-0 flex-col rounded-xl border border-border bg-card shadow-sm"
        aria-label="Interview progress panel"
    >
        <button
            type="button"
            class="flex w-full items-center justify-between p-4 text-left focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring xl:pointer-events-none"
            :aria-expanded="isExpandedMobile"
            @click="isExpandedMobile = !isExpandedMobile"
        >
            <span class="text-base font-semibold text-foreground">
                Interview Progress
            </span>
            <span class="text-xs font-medium text-primary xl:hidden">
                {{ isExpandedMobile ? 'Hide' : 'Show' }}
            </span>
        </button>

        <div
            class="flex min-h-0 flex-1 flex-col"
            :class="{ 'max-xl:hidden': !isExpandedMobile }"
        >
            <div
                role="tablist"
                aria-label="Interview panel tabs"
                class="flex gap-5 border-b border-border px-4"
            >
                <button
                    v-for="tab in tabs"
                    :id="`panel-tab-${tab.id}`"
                    :key="tab.id"
                    type="button"
                    role="tab"
                    class="-mb-px border-b-2 px-0.5 py-3 text-sm transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring"
                    :class="
                        activeTab === tab.id
                            ? 'border-primary font-medium text-primary'
                            : 'border-transparent text-muted-foreground hover:text-foreground'
                    "
                    :aria-selected="activeTab === tab.id"
                    :aria-controls="`panel-${tab.id}`"
                    @click="selectTab(tab.id)"
                    @keydown.arrow-right.prevent="selectTab('notes')"
                    @keydown.arrow-left.prevent="selectTab('progress')"
                >
                    {{ tab.label }}
                </button>
            </div>

            <div
                v-show="activeTab === 'progress'"
                id="panel-progress"
                role="tabpanel"
                aria-labelledby="panel-tab-progress"
                class="min-h-0 flex-1 overflow-y-auto p-4"
            >
                <template v-if="loading">
                    <Skeleton class="h-5 w-2/3" />
                    <Skeleton class="mt-3 h-2.5 w-full" />
                    <div class="mt-6 space-y-5">
                        <Skeleton
                            v-for="index in 5"
                            :key="index"
                            class="h-9 w-full"
                        />
                    </div>
                </template>

                <template v-else>
                    <h3 class="text-sm font-semibold text-foreground">
                        {{ jobTitle }}
                    </h3>

                    <div class="mt-3 flex items-center gap-3">
                        <div
                            class="h-2 flex-1 overflow-hidden rounded-full bg-muted"
                            role="progressbar"
                            :aria-valuenow="percentage"
                            aria-valuemin="0"
                            aria-valuemax="100"
                            :aria-label="`${percentage}% of interview completed`"
                        >
                            <div
                                class="h-full rounded-full bg-primary transition-all duration-700"
                                :style="{ width: `${percentage}%` }"
                            />
                        </div>
                        <span
                            class="text-sm font-semibold tabular-nums text-foreground"
                        >
                            {{ percentage }}%
                        </span>
                    </div>

                    <ol
                        v-if="sections.length > 0"
                        class="relative mt-6 space-y-6 pl-1"
                    >
                        <li
                            v-for="(section, index) in sections"
                            :key="section.name"
                            class="relative pl-8"
                        >
                            <span
                                v-if="index < sections.length - 1"
                                class="absolute top-7 bottom-[-24px] left-[11px] w-px bg-border"
                                aria-hidden="true"
                            />
                            <span
                                class="absolute top-0 left-0 flex size-6 items-center justify-center rounded-full"
                                :class="dotClass(section.status)"
                            >
                                <Check
                                    v-if="section.status === 'completed'"
                                    class="size-3.5"
                                    aria-hidden="true"
                                />
                            </span>

                            <div
                                class="flex items-baseline justify-between gap-2"
                            >
                                <p class="text-sm font-medium text-foreground">
                                    {{ section.name }}
                                </p>
                                <p
                                    class="shrink-0 text-xs tabular-nums text-muted-foreground"
                                >
                                    {{ section.completed }} / {{ section.total }}
                                </p>
                            </div>
                            <p
                                class="mt-0.5 text-xs"
                                :class="statusTextClass(section.status)"
                            >
                                {{ statusLabel(section.status) }}
                            </p>

                            <ul
                                v-if="section.children?.length"
                                class="mt-3 space-y-2.5 border-l border-border/70 pl-4"
                            >
                                <li
                                    v-for="child in section.children"
                                    :key="child.name"
                                    class="flex items-center justify-between gap-2 text-xs"
                                >
                                    <span class="truncate text-muted-foreground">
                                        {{ child.name }}
                                    </span>
                                    <span class="flex shrink-0 items-center gap-2">
                                        <span class="tabular-nums text-muted-foreground">
                                            {{ child.completed }} / {{ child.total }}
                                        </span>
                                        <span
                                            class="rounded-full px-2 py-0.5 text-[11px] font-medium"
                                            :class="
                                                statusBadgeClass(child.status)
                                            "
                                        >
                                            {{ statusLabel(child.status) }}
                                        </span>
                                    </span>
                                </li>
                            </ul>
                        </li>
                    </ol>

                    <p
                        v-else
                        class="py-6 text-center text-sm text-muted-foreground"
                    >
                        Sections will appear once the interview begins.
                    </p>
                </template>
            </div>

            <div
                v-show="activeTab === 'notes'"
                id="panel-notes"
                role="tabpanel"
                aria-labelledby="panel-tab-notes"
                class="min-h-0 flex-1 overflow-y-auto p-4"
            >
                <label for="interview-notes" class="sr-only">
                    Private interview notes
                </label>
                <textarea
                    id="interview-notes"
                    v-model="notes"
                    class="min-h-48 w-full resize-y rounded-lg border border-input bg-background p-3 text-sm text-foreground placeholder:text-muted-foreground focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-ring"
                    placeholder="Add private notes about this interview..."
                />
                <p class="mt-2 text-xs text-muted-foreground">
                    Notes are only visible to you.
                </p>
            </div>
        </div>
    </aside>
</template>
