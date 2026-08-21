<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BarChart3, ShieldCheck, Users } from '@lucide/vue';
import BrandName from '@/components/Brand/BrandName.vue';
import DashboardPreview from '@/components/Marketing/DashboardPreview.vue';
import FeatureItem from '@/components/Marketing/FeatureItem.vue';
import { home } from '@/routes';
</script>

<template>
    <div class="relative min-h-screen bg-[#F7F7FB]">
        <div class="grid min-h-screen lg:grid-cols-[0.9fr_1.1fr]">
            <!-- Left Marketing Panel -->
            <div class="relative hidden flex-col justify-between overflow-hidden p-12 xl:p-16 lg:flex">
                <!-- Decorative background -->
                <div class="absolute inset-0 bg-gradient-to-br from-[#F7F5FF] to-[#F7F7FB]">
                    <div class="absolute bottom-20 left-10 h-64 w-64 rounded-full bg-purple-200/20 blur-3xl" />
                    <div class="absolute top-40 right-10 h-48 w-48 rounded-full bg-purple-300/10 blur-2xl" />
                    <!-- Dot pattern -->
                    <div class="absolute bottom-40 left-16 grid grid-cols-5 gap-3 opacity-30">
                        <template v-for="i in 25" :key="i">
                            <div class="h-1.5 w-1.5 rounded-full bg-purple-400" />
                        </template>
                    </div>
                </div>

                <div class="relative z-10">
                    <Link :href="home()" class="inline-flex">
                        <BrandName tagline />
                    </Link>
                </div>

                <div class="relative z-10 space-y-8">
                    <div>
                        <h1 class="text-4xl font-semibold tracking-tight text-[#17171C] xl:text-5xl">
                            Create your<br />account
                        </h1>
                        <p class="mt-4 text-lg font-semibold text-purple-600">
                            Start smarter hiring today
                        </p>
                    </div>

                    <p class="max-w-md text-base leading-7 text-gray-600">
                        Join Skillora and transform the way you interview and hire top technical talent with the power of AI.
                    </p>

                    <div class="space-y-5">
                        <FeatureItem
                            :icon="Users"
                            title="AI-Powered Interviews"
                            description="Conduct technical interviews that adapt to each candidate in real-time."
                        />
                        <FeatureItem
                            :icon="BarChart3"
                            title="Data-Driven Insights"
                            description="Get detailed assessments and skill breakdowns to make confident decisions."
                        />
                        <FeatureItem
                            :icon="ShieldCheck"
                            title="Secure & Trustworthy"
                            description="Enterprise-grade security to keep your data and candidates safe."
                            icon-variant="green"
                        />
                    </div>
                </div>

                <div class="relative z-10">
                    <DashboardPreview />
                </div>
            </div>

            <!-- Right Registration Card -->
            <div class="flex items-center justify-center px-6 py-12 lg:px-12">
                <div class="w-full max-w-[600px]">
                    <!-- Mobile brand (shown only on mobile) -->
                    <div class="mb-8 lg:hidden">
                        <Link :href="home()" class="inline-flex">
                            <BrandName tagline />
                        </Link>
                        <h1 class="mt-6 text-3xl font-semibold tracking-tight text-[#17171C]">
                            Create your account
                        </h1>
                        <p class="mt-2 text-sm text-gray-500">
                            Start smarter hiring today
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-[#E8E8EF] bg-white p-8 shadow-[0_10px_30px_rgba(0,0,0,0.06)] sm:p-10 lg:p-12"
                    >
                        <div class="mb-8">
                            <h2 class="text-3xl font-semibold tracking-tight text-[#17171C]">
                                Create your account
                            </h2>
                            <p class="mt-2 text-sm text-gray-500">
                                Fill in the details below to get started.
                            </p>
                        </div>

                        <slot />
                    </div>

                    <p class="mt-6 text-center text-xs text-gray-500">
                        By signing up, you agree to our
                        <Link href="#" class="text-purple-600 hover:underline">Terms of Service</Link>
                        and
                        <Link href="#" class="text-purple-600 hover:underline">Privacy Policy</Link>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
