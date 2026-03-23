<script setup>
import AppLayout from "@/Components/Layouts/AppLayout.vue";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const token = ref("");
const verify = () => {
    router.post(
        route("guide.verify"),
        { qr_token: token.value },
        {
            onSuccess: () => alert("Visitor Access Granted!"),
            onError: (err) => alert("Invalid Pass: " + err.message),
        },
    );
};
</script>

<template>
    <AppLayout>
        <div
            class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-xl border border-blue-50"
        >
            <h1 class="text-2xl font-bold text-center text-blue-900 mb-6">
                QR Entry Scanner
            </h1>
            <div
                class="bg-gray-200 aspect-square rounded-xl mb-6 flex items-center justify-center border-2 border-dashed border-blue-300"
            >
                <p class="text-gray-500 font-medium italic">
                    Camera Viewport Active
                </p>
            </div>
            <input
                v-model="token"
                placeholder="Manual Token Entry"
                class="w-full mb-4 border-gray-300 rounded-lg shadow-sm"
            />
            <button
                @click="verify"
                class="w-full bg-blue-900 text-white font-bold py-3 rounded-lg hover:bg-blue-800 transition"
            >
                Verify Ticket
            </button>
        </div>
    </AppLayout>
</template>
