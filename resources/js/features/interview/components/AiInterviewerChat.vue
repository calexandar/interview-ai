<script setup lang="ts">
import { Sparkles } from '@lucide/vue';
import { computed } from 'vue';
import type { AiAgentStatus } from '@/types/interview';

const props = withDefaults(
    defineProps<{
        agentStatus?: AiAgentStatus;
        candidateName?: string;
        jobTitle?: string;
        /** The line the interviewer is actually saying right now. */
        message?: string | null;
        /** True while a question is being generated, so the panel can wait. */
        thinking?: boolean;
    }>(),
    {
        agentStatus: 'online',
        candidateName: '',
        jobTitle: '',
        message: null,
        thinking: false,
    },
);

const greeting = computed(() => {
    const name = props.candidateName.trim();
    const title = props.jobTitle.trim();

    const subject = title ? `for the ${title} role` : '';

    return `Welcome${name ? `, ${name}` : ''}. I'm your AI interviewer and I'll be asking you questions${subject}.`;
});

const prompt = computed(() => props.message?.trim() ?? '');

const timestamp = computed(() =>
    new Date().toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
    }),
);

const statusLabel = computed(() => {
    if (props.thinking) {
        return 'thinking';
    }

    return props.agentStatus;
});
</script>

<template>
    <div class="flex flex-col gap-3">
        <p class="flex items-center gap-2 text-sm font-semibold text-primary">
            <Sparkles class="size-4" aria-hidden="true" />
            AI Interviewer
        </p>

        <div
            class="max-w-sm rounded-xl border border-[#E7E7EF] bg-card p-4 shadow-sm dark:border-border"
        >
            <p class="text-sm leading-relaxed text-foreground">
                {{ greeting }}
            </p>

            <p
                v-if="thinking"
                class="mt-2 flex items-center gap-2 text-sm leading-relaxed text-muted-foreground"
                role="status"
                aria-live="polite"
            >
                <span
                    class="size-1.5 animate-pulse rounded-full bg-primary"
                    aria-hidden="true"
                />
                Let me think about the next question.
            </p>

            <p
                v-else-if="prompt"
                class="mt-2 text-sm leading-relaxed text-foreground"
            >
                {{ prompt }}
            </p>

            <p
                class="mt-3 flex items-center justify-between text-xs text-muted-foreground"
            >
                <span>{{ timestamp }}</span>
                <span class="flex items-center gap-1.5">
                    <span
                        class="size-1.5 rounded-full"
                        :class="thinking ? 'bg-primary' : 'bg-success'"
                        aria-hidden="true"
                    />
                    {{ statusLabel }}
                </span>
            </p>
        </div>
    </div>
</template>
