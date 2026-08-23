<script setup lang="ts">
import { Mic, Square } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import type { RecordingState } from '@/types/interview';

defineProps<{
    state: RecordingState;
}>();

const emit = defineEmits<{
    'toggle-recording': [];
    stop: [];
}>();
</script>

<template>
    <div class="flex flex-col items-center gap-2.5">
        <div class="flex items-center gap-4">
            <Button
                variant="outline"
                size="icon"
                class="size-12 rounded-full border-border bg-card shadow-sm hover:bg-muted"
                :aria-label="
                    state === 'paused'
                        ? 'Resume recording'
                        : 'Pause recording'
                "
                :disabled="state === 'processing' || state === 'completed'"
                @click="emit('toggle-recording')"
            >
                <Mic class="size-5 text-foreground" />
            </Button>

            <Button
                size="icon"
                class="size-12 rounded-full bg-destructive shadow-sm hover:bg-destructive/90"
                :aria-label="'Stop recording'"
                :disabled="state !== 'recording' && state !== 'paused'"
                @click="emit('stop')"
            >
                <Square class="size-4 fill-current" />
            </Button>
        </div>
        <p class="text-xs text-muted-foreground">
            <template v-if="state === 'recording'">
                Click to stop recording
            </template>
            <template v-else-if="state === 'paused'"> Recording paused </template>
            <template v-else-if="state === 'processing'">
                Processing your answer...
            </template>
            <template v-else> Answer recorded </template>
        </p>
    </div>
</template>
