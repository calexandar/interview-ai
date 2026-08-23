<script setup lang="ts">
import { ChevronDown, Moon, Sun } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useAppearance } from '@/composables/useAppearance';
import EndInterviewDialog from '@/features/interview/components/EndInterviewDialog.vue';
import InterviewTimer from '@/features/interview/components/InterviewTimer.vue';
import type { ActiveInterview } from '@/types/interview';

defineProps<{
    activeInterview: ActiveInterview | null;
}>();

const { appearance, updateAppearance } = useAppearance();

const isDark = computed(
    () => appearance.value === 'dark',
);

function toggleTheme(): void {
    updateAppearance(isDark.value ? 'light' : 'dark');
}
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-3 border-b border-border bg-card px-4 md:px-6"
    >
        <SidebarTrigger class="-ml-1 md:hidden" />

        <div class="flex min-w-0 items-center gap-2.5">
            <span
                class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-primary text-sm font-bold text-primary-foreground"
                aria-hidden="true"
            >
                AI
            </span>
            <span class="hidden items-center gap-1 sm:flex">
                <span class="text-base font-semibold text-foreground">
                    Interview AI
                </span>
                <ChevronDown
                    class="size-4 text-muted-foreground"
                    aria-hidden="true"
                />
            </span>
        </div>

        <template v-if="activeInterview">
            <Separator orientation="vertical" class="hidden !h-5 sm:block" />

            <div class="hidden min-w-0 items-center gap-2.5 sm:flex">
                <p class="truncate text-sm text-muted-foreground">
                    Interview in Progress
                </p>
                <p
                    class="flex items-center gap-1.5 text-sm font-medium text-success"
                >
                    <span class="relative flex size-2" aria-hidden="true">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-success opacity-60"
                        />
                        <span
                            class="relative inline-flex size-2 rounded-full bg-success"
                        />
                    </span>
                    Live
                </p>
            </div>
        </template>

        <div class="ml-auto flex items-center gap-2 md:gap-4">
            <EndInterviewDialog
                v-if="activeInterview"
                :interview-id="activeInterview.id"
            />

            <InterviewTimer
                v-if="activeInterview"
                :duration-seconds="activeInterview.durationSeconds"
                :remaining-seconds="activeInterview.remainingSeconds"
            />

            <Button
                variant="ghost"
                size="icon"
                class="size-9 text-muted-foreground hover:text-foreground"
                :aria-label="
                    isDark ? 'Switch to light mode' : 'Switch to dark mode'
                "
                @click="toggleTheme"
            >
                <Sun v-if="isDark" class="size-4.5" />
                <Moon v-else class="size-4.5" />
            </Button>
        </div>
    </header>
</template>
