<script setup>
import { useForm, router, Head } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    images: Array,
});

// --- STATE ---
const isEditing = ref(false);
const editId = ref(null);
const showSuccessPopup = ref(false);
const popupMessage = ref("");
const imagePreview = ref(null);
const searchQuery = ref("");
const selectedItems = ref([]);
const currentPage = ref(1);
const perPage = 8;
const isDragging = ref(false);

const form = useForm({
    title: "",
    image: null,
});

// --- COMPUTED ---
const filteredImages = computed(() => {
    return props.images.filter((img) =>
        img.title.toLowerCase().includes(searchQuery.value.toLowerCase()),
    );
});

const paginatedImages = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredImages.value.slice(start, start + perPage);
});

const totalPages = computed(() =>
    Math.ceil(filteredImages.value.length / perPage),
);

// --- FILE HANDLING ---
const handleFileChange = (e) => {
    const file = e.target.files[0];
    processFile(file);
};

const handleDrop = (e) => {
    e.preventDefault();
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    processFile(file);
};

const processFile = (file) => {
    if (file && file.type.startsWith("image/")) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

// --- ACTIONS ---
const triggerPopup = (msg) => {
    popupMessage.value = msg;
    showSuccessPopup.value = true;
    setTimeout(() => (showSuccessPopup.value = false), 3000);
};

const resetForm = () => {
    form.reset();
    isEditing.value = false;
    editId.value = null;
    imagePreview.value = null;
};

const submit = () => {
    if (!form.title) {
        alert("Please enter a title");
        return;
    }

    const routeName = isEditing.value
        ? route("admin.gallery.update", editId.value)
        : route("admin.gallery.store");

    form.post(routeName, {
        forceFormData: true,
        onSuccess: () => {
            triggerPopup(
                isEditing.value ? "Asset Updated ✨" : "Image Published 🚀",
            );
            resetForm();
        },
    });
};

const editImage = (img) => {
    window.scrollTo({ top: 0, behavior: "smooth" });
    isEditing.value = true;
    editId.value = img.id;
    form.title = img.title;
    imagePreview.value = img.url;
};

const deleteImage = (id) => {
    if (confirm("Are you sure you want to permanently remove this asset?")) {
        router.delete(route("admin.gallery.destroy", id), {
            onSuccess: () => triggerPopup("Deleted successfully"),
        });
    }
};

const deleteSelected = () => {
    if (confirm(`Delete ${selectedItems.value.length} selected items?`)) {
        selectedItems.value.forEach((id) => {
            router.delete(route("admin.gallery.destroy", id), {
                preserveScroll: true,
                onSuccess: () => {
                    if (
                        selectedItems.value.indexOf(id) ===
                        selectedItems.value.length - 1
                    ) {
                        triggerPopup("Bulk operation complete");
                        selectedItems.value = [];
                    }
                },
            });
        });
    }
};
</script>

<template>
    <Head title="Gallery Management" />

    <Transition name="fade-slide">
        <div
            v-if="showSuccessPopup"
            class="fixed top-6 right-6 z-50 bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-700"
        >
            <div
                class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"
            ></div>
            <span class="text-sm font-bold tracking-wide">{{
                popupMessage
            }}</span>
        </div>
    </Transition>

    <div class="min-h-screen bg-[#F8FAFC] p-4 md:p-10 font-sans text-slate-900">
        <header
            class="max-w-7xl mx-auto mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6"
        >
            <div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900">
                    Gallery <span class="text-indigo-600">Studio</span>
                </h1>
                <p class="text-slate-500 font-medium mt-1">
                    Manage your digital assets and visual story.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button
                    v-if="selectedItems.length"
                    @click="deleteSelected"
                    class="bg-rose-100 text-rose-600 px-6 py-3 rounded-xl font-bold text-sm hover:bg-rose-600 hover:text-white transition-all shadow-sm"
                >
                    Delete Selection ({{ selectedItems.length }})
                </button>
                <div
                    class="bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-200 flex items-center gap-2"
                >
                    <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                    <span
                        class="text-xs font-black text-slate-600 uppercase tracking-widest"
                        >Admin Mode</span
                    >
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-3 gap-8 mb-16">
                <div
                    class="lg:col-span-2 bg-white rounded-[2rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100"
                >
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-xl font-bold flex items-center gap-2">
                            <span
                                class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600"
                            >
                                i
                            </span>
                            {{
                                isEditing
                                    ? "Edit Existing Asset"
                                    : "Add New Asset"
                            }}
                        </h2>
                        <button
                            v-if="isEditing"
                            @click="resetForm"
                            class="text-xs font-bold text-slate-400 hover:text-rose-500 uppercase tracking-widest"
                        >
                            Cancel Edit
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div
                            @dragover.prevent="isDragging = true"
                            @dragleave="isDragging = false"
                            @drop="handleDrop"
                            :class="[
                                'relative border-2 border-dashed rounded-[1.5rem] p-10 transition-all duration-300 group cursor-pointer text-center',
                                isDragging
                                    ? 'border-indigo-500 bg-indigo-50/50 scale-[0.99]'
                                    : 'border-slate-200 hover:border-indigo-300 bg-slate-50/50',
                            ]"
                        >
                            <input
                                type="file"
                                @change="handleFileChange"
                                class="absolute inset-0 opacity-0 cursor-pointer z-10"
                            />
                            <div class="space-y-3">
                                <div
                                    class="w-16 h-16 bg-white rounded-2xl shadow-sm mx-auto flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"
                                >
                                    {{ isDragging ? "📥" : "📸" }}
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-slate-700">
                                        Drag images here
                                    </p>
                                    <p class="text-sm text-slate-400">
                                        or
                                        <span class="text-indigo-600 underline"
                                            >browse files</span
                                        >
                                        from device
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1"
                                    >Internal Title</label
                                >
                                <input
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Entry name..."
                                    class="w-full bg-slate-50 border-none rounded-xl p-4 text-slate-700 font-bold focus:ring-2 focus:ring-indigo-500 transition-all"
                                />
                            </div>
                            <div class="flex items-end">
                                <button
                                    @click="submit"
                                    :disabled="form.processing"
                                    class="w-full bg-indigo-600 text-white h-[56px] rounded-xl font-black tracking-widest text-sm hover:bg-slate-900 transition-all shadow-lg shadow-indigo-100 disabled:opacity-50"
                                >
                                    {{
                                        form.processing
                                            ? "UPLOADING..."
                                            : isEditing
                                              ? "UPDATE ASSET"
                                              : "PUBLISH ASSET"
                                    }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-slate-900 rounded-[2rem] p-8 text-white flex flex-col relative overflow-hidden group"
                >
                    <div class="relative z-10">
                        <h3
                            class="text-xs font-black text-indigo-400 uppercase tracking-[0.2em] mb-4"
                        >
                            Live Preview
                        </h3>

                        <div
                            v-if="imagePreview"
                            class="aspect-video rounded-2xl overflow-hidden border border-white/10 shadow-2xl bg-black/40 flex items-center justify-center"
                        >
                            <img
                                :src="imagePreview"
                                class="w-full h-full object-contain"
                                alt="Preview"
                            />
                        </div>

                        <div
                            v-else
                            class="aspect-video rounded-2xl border border-dashed border-white/20 flex flex-col items-center justify-center text-white/20"
                        >
                            <span class="text-4xl mb-2">👁️</span>
                            <p class="text-xs font-bold">Awaiting Content</p>
                        </div>

                        <div class="mt-6">
                            <div
                                class="h-6 w-3/4 bg-white/5 rounded-lg mb-2 overflow-hidden"
                            >
                                <div
                                    class="h-full bg-indigo-500 transition-all duration-500"
                                    :style="{
                                        width: form.title ? '100%' : '0%',
                                    }"
                                ></div>
                            </div>
                            <p class="text-xl font-bold truncate">
                                {{ form.title || "Untitled Asset" }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="absolute -bottom-20 -right-20 w-64 h-64 bg-indigo-600/20 rounded-full blur-3xl"
                    ></div>
                </div>
            </div>

            <div
                class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4"
            >
                <h2 class="text-2xl font-black text-slate-800">
                    Library Catalog
                </h2>
                <div class="relative w-full md:w-80">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Filter by title..."
                        class="w-full bg-white border border-slate-200 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold shadow-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                    />
                    <span
                        class="absolute left-4 top-1/2 -translate-y-1/2 grayscale"
                        >🔍</span
                    >
                </div>
            </div>

            <div
                v-if="filteredImages.length === 0"
                class="bg-white rounded-3xl p-20 text-center border-2 border-dashed border-slate-200"
            >
                <p class="text-slate-400 font-bold tracking-tight">
                    No assets found in the archive.
                </p>
            </div>

            <div
                class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
            >
                <div
                    v-for="img in paginatedImages"
                    :key="img.id"
                    class="group bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="relative h-48 overflow-hidden bg-slate-50">
                        <img
                            :src="img.url"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                        />
                        <div class="absolute top-4 left-4">
                            <input
                                type="checkbox"
                                :value="img.id"
                                v-model="selectedItems"
                                class="w-5 h-5 rounded-lg border-none bg-white/80 text-indigo-600 focus:ring-0 cursor-pointer"
                            />
                        </div>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4"
                        >
                            <div class="flex gap-2 w-full">
                                <button
                                    @click="editImage(img)"
                                    class="flex-1 bg-white/20 backdrop-blur-md text-white py-2 rounded-xl text-xs font-bold hover:bg-white hover:text-slate-900 transition-colors"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteImage(img.id)"
                                    class="flex-1 bg-rose-500/20 backdrop-blur-md text-white py-2 rounded-xl text-xs font-bold hover:bg-rose-500 transition-colors"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-slate-800 truncate">
                            {{ img.title }}
                        </h3>
                        <div class="flex items-center justify-between mt-4">
                            <span
                                class="text-[10px] font-black text-slate-300 uppercase tracking-tighter"
                                >#ADAM-{{ img.id }}</span
                            >
                            <div
                                class="flex gap-3 text-[11px] font-bold text-slate-500"
                            >
                                <span class="flex items-center gap-1"
                                    >❤️ {{ img.likes_count || 0 }}</span
                                >
                                <span class="flex items-center gap-1"
                                    >👁️ {{ img.views_count || 0 }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="totalPages > 1" class="flex justify-center mt-12 gap-3">
                <button
                    v-for="page in totalPages"
                    :key="page"
                    @click="currentPage = page"
                    :class="[
                        'w-10 h-10 rounded-xl font-bold text-sm transition-all shadow-sm',
                        page === currentPage
                            ? 'bg-indigo-600 text-white scale-110'
                            : 'bg-white text-slate-500 hover:bg-indigo-50',
                    ]"
                >
                    {{ page }}
                </button>
            </div>
        </main>
    </div>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(-20px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: scale(0.9);
}

input[type="checkbox"]:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
    background-color: #4f46e5;
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
}
</style>
