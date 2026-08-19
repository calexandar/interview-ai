<script setup lang="ts">
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';

interface Props {
    status: string;
}

const props = defineProps<Props>();

const config = computed(() => {
    switch (props.status) {
        case 'scheduled':
            return { label: 'Scheduled', variant: 'secondary' as const };
        case 'in_progress':
            return { label: 'In Progress', variant: 'default' as const };
        case 'paused':
            return { label: 'Paused', variant: 'secondary' as const };
        case 'completed':
            return { label: 'Completed', variant: 'default' as const };
        case 'cancelled':
            return { label: 'Cancelled', variant: 'destructive' as const };
        default:
            return { label: props.status, variant: 'secondary' as const };
    }
});
</script>

<template>
    <Badge
        :variant="config.variant"
        :class="{
            'bg-primary/10 text-primary hover:bg-primary/15': status === 'in_progress',
            'bg-success/10 text-success hover:bg-success/15': status === 'completed',
            'bg-muted text-muted-foreground': status === 'scheduled' || status === 'paused',
            'bg-destructive/10 text-destructive hover:bg-destructive/15': status === 'cancelled',
        }"
    >
        {{ config.label }}
    </Badge>
</template>
