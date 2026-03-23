<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    settings: Object,
});

const form = useForm({
    system_status: props.settings?.system_status || "active",
});

const submit = () => {
    form.post(route("admin.system.update"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="System Settings" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto">
            <div
                class="bg-white rounded-[32px] p-10 shadow-sm border border-slate-100"
            >
                <div class="mb-8">
                    <h2
                        class="text-2xl font-black uppercase tracking-tight text-slate-800"
                    >
                        System Availability
                    </h2>
                    <p class="text-slate-500 text-sm font-medium mt-1">
                        Control the global access state of the ACAGMS portal.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                    <div
                        v-for="status in ['active', 'maintenance', 'inactive']"
                        :key="status"
                        @click="form.system_status = status"
                        :class="[
                            form.system_status === status
                                ? 'border-indigo-600 bg-indigo-50/50 ring-4 ring-indigo-50'
                                : 'border-slate-100 bg-white hover:border-slate-200',
                            'p-6 rounded-3xl border-2 cursor-pointer transition-all relative overflow-hidden group',
                        ]"
                    >
                        <div class="flex flex-col gap-1 relative z-10">
                            <span
                                class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 group-hover:text-indigo-400"
                                >Mode</span
                            >
                            <span
                                class="font-black uppercase text-sm tracking-tight text-slate-700"
                                >{{ status }}</span
                            >
                        </div>
                        <div
                            v-if="form.system_status === status"
                            class="absolute top-4 right-4 w-2 h-2 rounded-full bg-indigo-600"
                        ></div>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end border-t border-slate-50 pt-8"
                >
                    <button
                        @click="submit"
                        :disabled="form.processing"
                        class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-bold uppercase text-[10px] tracking-[0.2em] hover:bg-indigo-600 transition-all disabled:opacity-50 shadow-xl shadow-slate-200"
                    >
                        {{
                            form.processing
                                ? "Syncing Protocol..."
                                : "Update System State"
                        }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
