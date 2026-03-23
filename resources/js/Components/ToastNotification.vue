<script setup>
import { ref, onMounted, watch } from "vue";

const props = defineProps({
    message: String,
    type: { type: String, default: "success" },
    duration: { type: Number, default: 3000 },
});

const show = ref(true);

onMounted(() => {
    setTimeout(() => {
        show.value = false;
    }, props.duration);
});
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed bottom-5 right-5 z-[100] max-w-sm w-full bg-white border-l-4 border-emerald-500 shadow-2xl rounded-xl p-4 flex items-center space-x-4"
        >
            <div class="flex-shrink-0 bg-emerald-100 p-2 rounded-lg">
                <span class="material-icons-outlined text-emerald-600"
                    >check_circle</span
                >
            </div>
            <div class="flex-1">
                <p
                    class="text-xs font-black uppercase tracking-widest text-zinc-400"
                >
                    System Message
                </p>
                <p class="text-sm font-bold text-zinc-900">{{ message }}</p>
            </div>
            <button
                @click="show = false"
                class="text-zinc-300 hover:text-zinc-500"
            >
                <span class="material-icons-outlined text-sm">close</span>
            </button>
        </div>
    </Transition>
</template>
