<script setup lang="ts">
import { Sparkles } from '@lucide/vue';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';

interface Props {
    recommendation: string;
}

const props = defineProps<Props>();

const config = computed(() => {
    switch (props.recommendation) {
        case 'strong_hire':
            return { label: 'Strong Hire', classes: 'bg-success/10 text-success' };
        case 'hire':
            return { label: 'Hire', classes: 'bg-success/10 text-success' };
        case 'mixed':
            return { label: 'Mixed', classes: 'bg-warning/10 text-warning' };
        case 'no_hire':
            return { label: 'No Hire', classes: 'bg-destructive/10 text-destructive' };
        case 'strong_no_hire':
            return { label: 'Strong No Hire', classes: 'bg-destructive/10 text-destructive' };
        default:
            return { label: props.recommendation, classes: 'bg-muted text-muted-foreground' };
    }
});
</script>

<template>
    <Badge variant="outline" :class="['gap-1 border-0', config.classes]">
        <Sparkles class="h-3 w-3" />
        {{ config.label }}
    </Badge>
</template>
