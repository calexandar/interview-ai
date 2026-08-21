<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BarChart3, ShieldCheck, Zap } from '@lucide/vue';
import BrandName from '@/components/Brand/BrandName.vue';
import DashboardPreview from '@/components/Marketing/DashboardPreview.vue';
import DecorativeDots from '@/components/Marketing/DecorativeDots.vue';
import FeatureItem from '@/components/Marketing/FeatureItem.vue';
import TestimonialCard from '@/components/Marketing/TestimonialCard.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-screen flex-col bg-[#F8F7FC]">
        <div
            class="pointer-events-none absolute inset-0"
            :style="{
                background:
                    'radial-gradient(circle at 35% 75%, rgba(109, 74, 255, 0.08), transparent 30%)',
            }"
        />

        <div class="relative grid flex-1 lg:grid-cols-[0.95fr_1.05fr]">
            <!-- Left Marketing Panel -->
            <div
                class="relative hidden flex-col justify-between gap-10 overflow-hidden px-12 py-12 lg:flex xl:px-16"
            >
                <div class="relative z-10">
                    <Link :href="home()" class="inline-flex">
                        <BrandName stacked />
                    </Link>
                </div>

                <div class="relative z-10 space-y-8">
                    <div>
                        <h1
                            class="text-5xl font-semibold tracking-tight text-[#17182B]"
                        >
                            Welcome back
                        </h1>
                        <p class="mt-4 text-lg font-semibold text-[#6D4AFF]">
                            Better interviews. Better hires.
                        </p>
                    </div>

                    <p class="max-w-md text-base leading-7 text-gray-600">
                        Log in to your Skillora account and continue building
                        strong technical teams with the power of AI.
                    </p>

                    <div class="space-y-5">
                        <FeatureItem
                            :icon="Zap"
                            title="Smarter Interviews"
                            description="Adaptive, AI-powered interviews for every candidate."
                        />
                        <FeatureItem
                            :icon="BarChart3"
                            title="Clearer Insights"
                            description="Objective assessments and skill breakdowns, all in one place."
                        />
                        <FeatureItem
                            :icon="ShieldCheck"
                            title="A More Confident Hire"
                            description="Make hiring decisions with real data, not guesswork."
                            icon-variant="green"
                        />
                    </div>
                </div>

                <div class="relative z-10">
                    <div class="relative inline-block">
                        <div
                            class="absolute -inset-6 rounded-full bg-purple-200/30 blur-3xl"
                        />
                        <div class="relative">
                            <DashboardPreview />
                        </div>
                        <DecorativeDots class="absolute -bottom-10 -left-16" />
                    </div>

                    <p
                        class="mt-14 text-xs font-medium tracking-wide text-gray-400 uppercase"
                    >
                        Built with technologies trusted by modern teams
                    </p>
                    <div class="mt-3 flex items-center gap-6">
                        <span class="text-sm font-semibold text-gray-400"
                            >Laravel</span
                        >
                        <span class="text-sm font-semibold text-gray-400"
                            >Vue.js</span
                        >
                        <span class="text-sm font-semibold text-gray-400"
                            >AWS</span
                        >
                        <span class="text-sm font-semibold text-gray-400"
                            >Google</span
                        >
                    </div>
                </div>
            </div>

            <!-- Right Login Panel -->
            <div
                class="relative flex flex-col items-center justify-center px-4 py-8 sm:px-8 lg:px-12 lg:py-12"
            >
                <div class="w-full max-w-[540px]">
                    <!-- Testimonial (shown only on xl+) -->
                    <div class="mb-6 hidden justify-end xl:flex">
                        <TestimonialCard
                            quote="Skillora has completely changed how we screen technical talent."
                            name="Emily Carter"
                            role="Head of Engineering, Pixelate"
                            initials="EC"
                        />
                    </div>

                    <!-- Mobile brand (shown only below lg) -->
                    <div class="mb-8 flex justify-center lg:hidden">
                        <Link :href="home()" class="inline-flex">
                            <BrandName stacked class="items-center" />
                        </Link>
                    </div>

                    <div
                        class="rounded-[18px] border border-[#E7E7EF] bg-white px-6 py-8 shadow-[0_20px_60px_rgba(23,24,43,0.06)] sm:px-10 sm:py-10 lg:px-12 lg:py-12"
                    >
                        <slot />
                    </div>

                    <div
                        class="mx-auto mt-6 flex max-w-sm items-center justify-center gap-2 text-center"
                    >
                        <ShieldCheck class="h-4 w-4 shrink-0 text-gray-400" />
                        <p class="text-xs text-gray-500">
                            Your account and interview data are protected with
                            secure authentication.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <footer class="relative py-6">
            <p
                class="px-4 text-center text-xs text-gray-400 sm:px-6 lg:text-center"
            >
                Skillora &bull; AI-powered technical interviews &bull; Smarter
                hiring for a stronger tomorrow.
            </p>
        </footer>
    </div>
</template>
