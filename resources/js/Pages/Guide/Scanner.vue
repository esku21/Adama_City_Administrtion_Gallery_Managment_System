<script setup>
import AppLayout from "@/Components/Layouts/AppLayout.vue";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const scannerActive = ref(false);

// Initialize with the translation key instead of hardcoded text
const statusMessage = ref(t("guide_scanner.ready"));

const startScanner = () => {
    scannerActive.ref = true;
    statusMessage.value = t("guide_scanner.scanning");
};
</script>

<template>
    <AppLayout>
        <div class="max-w-md mx-auto text-center py-10">
            <h1
                class="text-2xl font-black uppercase tracking-tight text-slate-800 mb-6"
            >
                {{ t("guide_scanner.title") }}
            </h1>

            <div
                class="relative bg-slate-900 aspect-square rounded-[2rem] overflow-hidden mb-8 flex items-center justify-center shadow-2xl shadow-indigo-100 border-4 border-white"
            >
                <div v-if="!scannerActive" class="text-white">
                    <button
                        @click="startScanner"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-black uppercase text-xs tracking-widest transition-all transform active:scale-95 shadow-lg shadow-indigo-500/20"
                    >
                        {{ t("guide_scanner.start_camera") }}
                    </button>
                </div>

                <div
                    v-else
                    class="relative w-full h-full flex items-center justify-center"
                >
                    <div
                        class="absolute inset-0 bg-gradient-to-b from-indigo-500/10 to-transparent animate-pulse"
                    ></div>
                    <div
                        class="text-emerald-400 border-2 border-dashed border-emerald-400/50 w-64 h-64 rounded-3xl animate-[pulse_2s_infinite]"
                    ></div>
                    <div
                        class="absolute h-0.5 w-64 bg-emerald-400 shadow-[0_0_15px_rgba(52,211,153,0.8)] top-1/2 -translate-y-1/2 animate-[scan_2s_ease-in-out_infinite]"
                    ></div>
                </div>
            </div>

            <div
                class="p-6 rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all border"
                :class="
                    scannerActive
                        ? 'bg-indigo-50 border-indigo-100 text-indigo-600'
                        : 'bg-slate-50 border-slate-100 text-slate-400'
                "
            >
                {{ statusMessage }}
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes scan {
    0%,
    100% {
        transform: translateY(-120px);
    }
    50% {
        transform: translateY(120px);
    }
}

/* Custom shadow for better depth */
.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
</style>
