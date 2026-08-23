<script setup lang="ts">
import { Sparkles } from '@lucide/vue';
import { computed } from 'vue';
import type { AiAgentStatus } from '@/types/interview';

withDefaults(
    defineProps<{
        agentStatus?: AiAgentStatus;
    }>(),
    {
        agentStatus: 'online',
    },
);

const timestamp = computed(() =>
    new Date().toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
    }),
);
</script>

<template>
    <div class="flex flex-col gap-3">
        <p
            class="flex items-center gap-2 text-sm font-semibold text-primary"
        >
            <Sparkles class="size-4" aria-hidden="true" />
            AI Interviewer
        </p>

        <div
            class="max-w-sm rounded-xl border border-[#E7E7EF] bg-card p-4 shadow-sm dark:border-border"
        >
            <p class="text-sm leading-relaxed text-foreground">
                Welcome! I'm your AI interviewer. I'll be asking you questions
                to assess your skills in web development.
            </p>
            <p class="mt-2 text-sm leading-relaxed text-foreground">
                Let's start with a question.
            </p>
            <p
                class="mt-3 flex items-center justify-between text-xs text-muted-foreground"
            >
                <span>{{ timestamp }}</span>
                <span class="flex items-center gap-1.5">
                    <span
                        class="size-1.5 rounded-full bg-success"
                        aria-hidden="true"
                    />
                    {{ agentStatus }}
                </span>
            </p>
        </div>
    </div>
</template>
