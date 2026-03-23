<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    guides: Array,
    halls: Array,
});

// --- SEARCH & FILTER STATE ---
const searchQuery = ref("");
const selectedHall = ref("");

const filteredGuides = computed(() => {
    return props.guides.filter((guide) => {
        const query = searchQuery.value.toLowerCase();
        const matchesSearch =
            guide.name.toLowerCase().includes(query) ||
            guide.email.toLowerCase().includes(query);

        const matchesHall =
            selectedHall.value === "" || guide.hall_id == selectedHall.value;

        return matchesSearch && matchesHall;
    });
});

// --- FORM STATE (SINGLE) ---
const isEditing = ref(false);
const editingId = ref(null);
const form = useForm({
    name: "",
    email: "",
    phone: "",
    gender: "",
    hall_id: "",
});

// --- BULK UPLOAD STATE ---
const bulkForm = useForm({
    file: null,
});

const handleFileUpload = (e) => {
    bulkForm.file = e.target.files[0];
};

const submitBulkImport = () => {
    if (!bulkForm.file) {
        Swal.fire("Error", "Please select a CSV file first.", "error");
        return;
    }

    bulkForm.post(route("admin.guides.import"), {
        onSuccess: () => {
            bulkForm.reset();
            Swal.fire(
                "Success",
                "Staff members imported successfully!",
                "success",
            );
        },
        onError: () => {
            Swal.fire("Error", "Import failed. Check file format.", "error");
        },
    });
};

const submit = () => {
    if (isEditing.value) {
        form.put(route("admin.guides.update", editingId.value), {
            onSuccess: () => {
                closeModal();
                Swal.fire("Updated", "Staff profile saved.", "success");
            },
        });
    } else {
        form.post(route("admin.guides.store"), {
            onSuccess: () => {
                form.reset();
                Swal.fire("Saved", "New staff member assigned.", "success");
            },
        });
    }
};

const editGuide = (guide) => {
    isEditing.value = true;
    editingId.value = guide.id;
    form.name = guide.name;
    form.email = guide.email;
    form.phone = guide.phone;
    form.gender = guide.gender;
    form.hall_id = guide.hall_id;
    window.scrollTo({ top: 0, behavior: "smooth" });
};

const closeModal = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
};
</script>

<template>
    <Head title="Guides Management" />

    <AuthenticatedLayout>
        <div class="p-8 space-y-8 w-full">
            <div
                class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm"
            >
                <div>
                    <h2
                        class="text-3xl font-black tracking-tight text-slate-900 uppercase"
                    >
                        Guides <span class="text-indigo-600">Management</span>
                    </h2>
                    <p
                        class="text-[10px] text-slate-400 uppercase tracking-[0.2em] font-bold mt-1"
                    >
                        System Index • {{ filteredGuides.length }} Active Staff
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative flex items-center group">
                        <span
                            class="material-icons-outlined absolute left-4 text-slate-400 text-lg group-focus-within:text-indigo-500 transition-colors pointer-events-none"
                            >search</span
                        >
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search by name..."
                            class="bg-slate-50 border border-slate-200 rounded-2xl py-3 pl-12 pr-4 text-xs w-72 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-bold text-slate-700"
                        />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div
                    class="lg:col-span-2 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden"
                >
                    <div
                        v-if="isEditing"
                        class="absolute top-0 left-0 w-full h-1.5 bg-indigo-500 animate-pulse"
                    ></div>
                    <div class="flex items-center gap-3 mb-8">
                        <div
                            class="h-10 w-10 bg-indigo-50 rounded-2xl flex items-center justify-center"
                        >
                            <span
                                class="material-icons-outlined text-indigo-600 text-xl"
                                >{{
                                    isEditing ? "edit_note" : "person_add"
                                }}</span
                            >
                        </div>
                        <h3
                            class="text-xs font-black uppercase text-slate-800 tracking-[0.1em]"
                        >
                            {{
                                isEditing
                                    ? "Update Profile"
                                    : "Single Assignment"
                            }}
                        </h3>
                    </div>

                    <form
                        @submit.prevent="submit"
                        class="grid grid-cols-1 md:grid-cols-2 gap-6"
                    >
                        <div class="space-y-1.5">
                            <label class="label-style">Full Name</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="input-style"
                                placeholder="Enter name"
                                required
                            />
                        </div>
                        <div class="space-y-1.5">
                            <label class="label-style">Email Address</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="input-style"
                                placeholder="Enter email"
                                required
                            />
                        </div>
                        <div class="space-y-1.5">
                            <label class="label-style">Phone</label>
                            <input
                                v-model="form.phone"
                                type="text"
                                class="input-style"
                                placeholder="+251..."
                                required
                            />
                        </div>
                        <div class="space-y-1.5">
                            <label class="label-style">Assign Hall</label>
                            <select
                                v-model="form.hall_id"
                                class="input-style"
                                required
                            >
                                <option
                                    v-for="hall in halls"
                                    :key="hall.id"
                                    :value="hall.id"
                                >
                                    {{ hall.name }}
                                </option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex gap-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="btn-primary flex-1 uppercase"
                            >
                                {{
                                    isEditing
                                        ? "Update Staff"
                                        : "Confirm Assignment"
                                }}
                            </button>
                            <button
                                v-if="isEditing"
                                @click="closeModal"
                                type="button"
                                class="btn-secondary px-6"
                            >
                                <span class="material-icons-outlined"
                                    >close</span
                                >
                            </button>
                        </div>
                    </form>
                </div>

                <div
                    class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-2xl shadow-indigo-200 text-white"
                >
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="h-10 w-10 bg-white/10 rounded-2xl flex items-center justify-center"
                        >
                            <span
                                class="material-icons-outlined text-white text-xl"
                                >upload_file</span
                            >
                        </div>
                        <h3
                            class="text-xs font-black uppercase tracking-[0.1em]"
                        >
                            Bulk CSV Import
                        </h3>
                    </div>

                    <p
                        class="text-indigo-200 text-[10px] leading-relaxed mb-6 font-medium"
                    >
                        Upload a CSV file with columns: <br />
                        <span class="text-white font-mono"
                            >name, email, phone, gender, hall_id</span
                        >
                    </p>

                    <form @submit.prevent="submitBulkImport" class="space-y-4">
                        <div class="relative group">
                            <input
                                type="file"
                                @change="handleFileUpload"
                                accept=".csv"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            />
                            <div
                                class="border-2 border-dashed border-white/20 rounded-2xl p-6 text-center group-hover:border-indigo-400 transition-all bg-white/5"
                            >
                                <span
                                    class="material-icons-outlined text-indigo-300 mb-2"
                                    >cloud_upload</span
                                >
                                <p
                                    class="text-[9px] font-black uppercase tracking-tighter"
                                >
                                    {{
                                        bulkForm.file
                                            ? bulkForm.file.name
                                            : "Select CSV File"
                                    }}
                                </p>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="bulkForm.processing"
                            class="w-full bg-white text-indigo-900 font-black py-4 rounded-2xl uppercase text-[10px] tracking-widest hover:bg-indigo-50 transition-all disabled:opacity-50"
                        >
                            {{
                                bulkForm.processing
                                    ? "Importing..."
                                    : "Start Bulk Assign"
                            }}
                        </button>
                    </form>
                </div>
            </div>

            <div
                class="bg-white rounded-[2rem] border border-slate-200 overflow-hidden shadow-sm"
            >
                <table class="w-full text-left">
                    <thead
                        class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-[0.25em] border-b border-slate-100"
                    >
                        <tr>
                            <th class="py-6 px-8 font-black">Staff Member</th>
                            <th class="py-6 px-4 font-black">Location</th>
                            <th class="py-6 px-8 font-black text-center w-44">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr
                            v-for="guide in filteredGuides"
                            :key="guide.id"
                            class="hover:bg-slate-50/50 transition-all group"
                        >
                            <td class="py-6 px-8">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="h-10 w-10 bg-indigo-600 text-white rounded-2xl flex items-center justify-center font-black text-xs uppercase"
                                    >
                                        {{ guide.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <div
                                            class="font-bold text-slate-900 text-sm leading-tight"
                                        >
                                            {{ guide.name }}
                                        </div>
                                        <div
                                            class="text-[10px] text-slate-400 font-medium"
                                        >
                                            {{ guide.email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-4">
                                <span
                                    class="bg-slate-100 text-slate-500 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border border-slate-200 group-hover:bg-indigo-600 group-hover:text-white transition-all"
                                >
                                    {{ guide.hall?.name || "Unassigned" }}
                                </span>
                            </td>
                            <td class="py-6 px-8 flex justify-center gap-2">
                                <button
                                    @click="editGuide(guide)"
                                    class="icon-action text-indigo-600 hover:bg-indigo-50"
                                >
                                    <span class="material-icons-outlined"
                                        >edit</span
                                    >
                                </button>
                                <Link
                                    :href="
                                        route('admin.guides.delete', guide.id)
                                    "
                                    method="delete"
                                    as="button"
                                    class="icon-action text-rose-500 hover:bg-rose-50"
                                    ><span class="material-icons-outlined"
                                        >delete</span
                                    ></Link
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.label-style {
    @apply text-[10px] text-slate-500 uppercase font-black tracking-widest block mb-1.5;
}
.input-style {
    @apply w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-bold text-slate-700;
}
.btn-primary {
    @apply bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl transition-all shadow-lg shadow-indigo-100 active:scale-95;
}
.btn-secondary {
    @apply bg-slate-100 hover:bg-slate-200 text-slate-600 font-black py-4 rounded-2xl transition-all active:scale-95;
}
.icon-action {
    @apply h-10 w-10 flex items-center justify-center rounded-2xl transition-all;
}
</style>
