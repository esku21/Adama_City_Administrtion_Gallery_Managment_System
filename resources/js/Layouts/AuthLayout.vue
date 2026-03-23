<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="min-h-screen bg-[#f8fafc] flex flex-col items-center justify-center p-4 font-sans"
    >
        <div class="w-full max-w-2xl">
            <slot />
        </div>

        <div class="mt-8 text-center">
            <p
                class="text-slate-400 text-xs font-medium uppercase tracking-widest"
            >
                Adama City Administration Gallery Portal
            </p>
        </div>
    </div>
</template>

<style scoped>
/* You can add custom background patterns here if you want a professional look */
.min-h-screen {
    background-image: radial-gradient(#e2e8f0 0.5px, transparent 0.5px);
    background-size: 24px 24px;
}
</style>
