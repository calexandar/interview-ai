<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';
import { end } from '@/routes/interviews';

const props = defineProps<{
    interviewId: number;
}>();

const isOpen = ref(false);
const isEnding = ref(false);

function confirmEndInterview(): void {
    isEnding.value = true;

    router.post(
        end({ interview: props.interviewId }),
        {},
        {
            onFinish: () => {
                isEnding.value = false;
                isOpen.value = false;
            },
        },
    );
}
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button
                variant="outline"
                class="border-destructive/40 text-destructive hover:bg-destructive/10 hover:text-destructive"
            >
                End Interview
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>End this interview?</DialogTitle>
                <DialogDescription>
                    Are you sure you want to end the interview? The
                    candidate's current progress will be saved.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2 sm:gap-0">
                <Button
                    variant="outline"
                    :disabled="isEnding"
                    @click="isOpen = false"
                >
                    Cancel
                </Button>
                <Button
                    variant="destructive"
                    :disabled="isEnding"
                    @click="confirmEndInterview"
                >
                    <Spinner v-if="isEnding" class="size-4" />
                    End Interview
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
