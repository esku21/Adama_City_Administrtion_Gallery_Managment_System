<script setup>
import AppLayout from "@/Components/Layouts/AppLayout.vue";
import { ref } from "vue";

const scannerActive = ref(false);
const statusMessage = ref("Ready to scan...");
</script>

<template>
    <AppLayout>
        <div class="max-w-md mx-auto text-center">
            <h1 class="text-2xl font-bold mb-4">Entry Verification</h1>
            <div
                class="relative bg-black aspect-square rounded-2xl overflow-hidden mb-6 flex items-center justify-center"
            >
                <div v-if="!scannerActive" class="text-white">
                    <button
                        @click="scannerActive = true"
                        class="bg-blue-600 px-6 py-2 rounded-full font-bold"
                    >
                        Start Camera
                    </button>
                </div>
                <div
                    v-else
                    class="text-white border-2 border-dashed border-green-400 w-64 h-64 animate-pulse"
                ></div>
            </div>
            <div
                class="p-4 rounded-lg text-lg font-bold"
                :class="{ 'bg-gray-100': true }"
            >
                {{ statusMessage }}
            </div>
        </div>
    </AppLayout>
</template>
