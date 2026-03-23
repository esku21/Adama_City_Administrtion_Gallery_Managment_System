<script setup>
import { useForm, usePage, Head } from "@inertiajs/vue3";
import { computed } from "vue";
import Swal from "sweetalert2";

const user = computed(() => usePage().props.auth.user);

const form = useForm({
    firstName: user.value?.firstName || "",
    lastName: user.value?.lastName || "",
    email: user.value?.email || "",
    phone_no: user.value?.phone_no || "",
});

const updateProfile = () => {
    form.patch(route("visitor.profile.update"), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: "Profile Updated",
                text: "Your personal information has been saved.",
                icon: "success",
                confirmButtonColor: "#2563eb",
            });
        },
    });
};
</script>

<template>
    <Head title="Personal Information" />
    <div class="min-h-screen bg-slate-50 p-10">
        <div class="max-w-3xl mx-auto">
            <h1
                class="text-3xl font-black text-slate-900 uppercase tracking-tight mb-8"
            >
                Personal Information
            </h1>

            <div
                class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 p-10"
            >
                <form @submit.prevent="updateProfile" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2"
                                >First Name</label
                            >
                            <input
                                v-model="form.firstName"
                                type="text"
                                class="w-full border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 transition-all"
                                required
                            />
                            <p
                                v-if="form.errors.firstName"
                                class="text-red-500 text-xs mt-2"
                            >
                                {{ form.errors.firstName }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2"
                                >Last Name</label
                            >
                            <input
                                v-model="form.lastName"
                                type="text"
                                class="w-full border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 transition-all"
                                required
                            />
                            <p
                                v-if="form.errors.lastName"
                                class="text-red-500 text-xs mt-2"
                            >
                                {{ form.errors.lastName }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2"
                            >Email Address</label
                        >
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 transition-all"
                            required
                        />
                        <p
                            v-if="form.errors.email"
                            class="text-red-500 text-xs mt-2"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2"
                            >Phone Number</label
                        >
                        <input
                            v-model="form.phone_no"
                            type="text"
                            class="w-full border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 transition-all"
                        />
                    </div>

                    <div class="pt-4">
                        <button
                            :disabled="form.processing"
                            class="px-8 py-4 bg-[#0f172a] hover:bg-blue-600 text-white rounded-2xl font-bold uppercase text-xs tracking-widest transition-all shadow-lg hover:shadow-blue-600/20"
                        >
                            {{ form.processing ? "Saving..." : "Save Changes" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
