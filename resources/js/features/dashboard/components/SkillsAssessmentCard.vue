<script setup lang="ts">
import { computed } from 'vue';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import type { SkillScore } from '@/types/interview';

const props = defineProps<{
    skills: SkillScore[];
    loading?: boolean;
}>();

const CENTER = 110;
const RADIUS = 76;
const SIZE = 220;

const AXES_COUNT = 6;

function axisAngle(index: number): number {
    return (-90 + (360 / AXES_COUNT) * index) * (Math.PI / 180);
}

function pointAt(index: number, value: number): { x: number; y: number } {
    const angle = axisAngle(index);
    const r = (value / 100) * RADIUS;

    return {
        x: CENTER + r * Math.cos(angle),
        y: CENTER + r * Math.sin(angle),
    };
}

const rings = [25, 50, 75, 100].map((level) => ({
    level,
    points: Array.from({ length: AXES_COUNT }, (_, i) => pointAt(i, level))
        .map((p) => `${p.x},${p.y}`)
        .join(' '),
}));

const spokes = Array.from({ length: AXES_COUNT }, (_, i) => pointAt(i, 100));

const scorePolygon = computed(() =>
    props.skills.length === AXES_COUNT
        ? props.skills
              .map((skill, i) => {
                  const p = pointAt(i, Math.min(100, Math.max(0, skill.score)));

                  return `${p.x},${p.y}`;
              })
              .join(' ')
        : '',
);

interface LabelPosition {
    text: string;
    x: number;
    y: number;
    anchor: 'start' | 'middle' | 'end';
    value: number;
}

const labels = computed<LabelPosition[]>(() =>
    props.skills.map((skill, i) => {
        const angle = axisAngle(i);
        const labelRadius = RADIUS + 18;
        const x = CENTER + labelRadius * Math.cos(angle);
        const y = CENTER + labelRadius * Math.sin(angle);

        return {
            text: skill.skill,
            value: skill.score,
            x,
            y,
            anchor:
                Math.cos(angle) > 0.3
                    ? 'start'
                    : Math.cos(angle) < -0.3
                      ? 'end'
                      : 'middle',
        };
    }),
);

const summary = computed(() =>
    props.skills.map((s) => `${s.skill} ${s.score}%`).join(', '),
);
</script>

<template>
    <Card class="gap-4 rounded-xl border-border shadow-sm">
        <CardHeader>
            <CardTitle class="text-base font-semibold">
                Skills Assessment
                <span class="font-normal text-muted-foreground">(Live)</span>
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
            <Skeleton v-if="loading" class="mx-auto aspect-square w-full max-w-[260px]" />

            <template v-else>
                <div
                    v-if="skills.length === AXES_COUNT"
                    class="relative mx-auto w-full max-w-[280px]"
                >
                    <svg
                        :viewBox="`0 0 ${SIZE} ${SIZE}`"
                        class="h-auto w-full"
                        role="img"
                        aria-label="Live skills assessment"
                    >
                        <title>Skill scores for the active interview</title>

                        <polygon
                            v-for="ring in rings"
                            :key="ring.level"
                            :points="ring.points"
                            fill="none"
                            class="stroke-border"
                            stroke-width="1"
                        />

                        <line
                            v-for="(spoke, index) in spokes"
                            :key="index"
                            :x1="CENTER"
                            :y1="CENTER"
                            :x2="spoke.x"
                            :y2="spoke.y"
                            class="stroke-border/70"
                            stroke-width="1"
                        />

                        <polygon
                            v-if="scorePolygon"
                            :points="scorePolygon"
                            class="fill-primary/15 stroke-primary"
                            stroke-width="2"
                            stroke-linejoin="round"
                        />

                        <circle
                            v-for="(label, index) in labels"
                            :key="`dot-${index}`"
                            :cx="
                                CENTER +
                                ((RADIUS * Math.min(100, label.value)) / 100) *
                                    Math.cos(axisAngle(index))
                            "
                            :cy="
                                CENTER +
                                ((RADIUS * Math.min(100, label.value)) / 100) *
                                    Math.sin(axisAngle(index))
                            "
                            r="3"
                            class="fill-primary"
                        />

                        <text
                            v-for="(label, index) in labels"
                            :key="`label-${index}`"
                            :x="label.x"
                            :y="label.y"
                            :text-anchor="label.anchor"
                            dominant-baseline="middle"
                            class="fill-muted-foreground text-[9px]"
                        >
                            {{ label.text }}
                        </text>
                    </svg>
                </div>

                <div
                    v-else-if="skills.length > 0"
                    class="flex flex-wrap gap-2"
                >
                    <span
                        v-for="skill in skills"
                        :key="skill.skill"
                        class="inline-flex items-center gap-2 rounded-full bg-primary/8 px-3 py-1 text-xs font-medium text-primary"
                    >
                        {{ skill.skill }}
                        <span class="tabular-nums">{{ skill.score }}%</span>
                    </span>
                </div>

                <p
                    v-else
                    class="rounded-lg border border-dashed border-border bg-muted/30 px-6 py-10 text-center text-sm text-muted-foreground"
                    role="status"
                >
                    Skills assessment will appear once the interview begins.
                </p>
            </template>

            <footer
                v-if="!loading && skills.length === AXES_COUNT"
                class="flex items-center gap-2 text-xs text-muted-foreground"
            >
                <span
                    class="size-1.5 shrink-0 rounded-full bg-primary"
                    aria-hidden="true"
                />
                Scores update in real-time as the interview progresses
            </footer>

            <p class="sr-only" role="status">{{ summary }}</p>
        </CardContent>
    </Card>
</template>
