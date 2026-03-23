<script setup>
import { Link } from "@inertiajs/vue3";
import LanguageSwitcher from "@/Components/Shared/LanguageSwitcher.vue";
</script>

<template>
    <div
        class="min-h-screen bg-blue-50 flex flex-col items-center justify-center p-6"
    >
        <div class="absolute top-0 right-0 p-6">
            <LanguageSwitcher />
        </div>

        <div
            class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden border border-blue-100"
        >
            <div class="bg-blue-900 p-6 text-center">
                <h1
                    class="text-white text-2xl font-bold tracking-widest uppercase"
                >
                    ACAGMS
                </h1>
                <p class="text-blue-200 text-xs">
                    Adama City Gallery Management
                </p>
            </div>

            <div class="p-8">
                <slot />
            </div>
        </div>

        <div class="mt-6 text-sm text-gray-500">
            <Link :href="'/'" class="hover:text-blue-900 underline"
                >Back to Home</Link
            >
        </div>
    </div>
</template>
