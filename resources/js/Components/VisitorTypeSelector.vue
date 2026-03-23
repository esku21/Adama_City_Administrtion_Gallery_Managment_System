<script setup>
import { computed } from "vue";

const props = defineProps({
    modelValue: String,
    orgValue: String,
});

const emit = defineEmits(["update:modelValue", "update:orgValue"]);

// This handles the radio button selection
const selectedRadio = computed({
    get: () => {
        // If it's a gov authority, return the base type
        if (props.modelValue?.includes("Federal-Authority"))
            return "Federal-Authority";
        if (props.modelValue?.includes("Regional-Authority"))
            return "Regional-Authority";
        return props.modelValue;
    },
    set: (val) => {
        emit("update:modelValue", val);
        // Clear org name if switching away from Government
        if (!val.includes("Authority")) {
            emit("update:orgValue", "");
        }
    },
});

const isGovernment = computed(() => {
    return selectedRadio.value?.includes("Authority");
});
</script>

<template>
    <div class="border border-slate-200 rounded-3xl p-6 bg-slate-50 shadow-sm">
        <h3
            class="text-[10px] font-black text-slate-400 mb-4 uppercase tracking-wider"
        >
            VIP Category Details
        </h3>

        <div class="space-y-4">
            <div
                class="flex flex-wrap gap-4 p-3 bg-white rounded-2xl border border-slate-100"
            >
                <label
                    class="flex items-center gap-2 cursor-pointer text-sm font-bold text-slate-700"
                >
                    <input
                        type="radio"
                        value="Federal-Authority"
                        v-model="selectedRadio"
                        class="w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                    />
                    <span>Federal Authority</span>
                </label>

                <label
                    class="flex items-center gap-2 cursor-pointer text-sm font-bold text-slate-700"
                >
                    <input
                        type="radio"
                        value="Regional-Authority"
                        v-model="selectedRadio"
                        class="w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                    />
                    <span>Regional Authority</span>
                </label>
            </div>

            <div v-if="isGovernment" class="mt-2 transition-all duration-300">
                <label
                    class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1"
                    >Organization Name</label
                >
                <input
                    type="text"
                    :value="orgValue"
                    @input="$emit('update:orgValue', $event.target.value)"
                    placeholder="e.g. Ministry of Culture"
                    class="block w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-3 font-bold"
                    required
                />
            </div>

            <div class="grid grid-cols-1 gap-3 pt-2 border-t border-slate-200">
                <label
                    class="flex items-center gap-2 cursor-pointer text-sm font-bold text-slate-700"
                >
                    <input
                        type="radio"
                        value="NGO"
                        v-model="selectedRadio"
                        class="w-4 h-4 text-indigo-600"
                    />
                    <span>NGO</span>
                </label>

                <label
                    class="flex items-center gap-2 cursor-pointer text-sm font-bold text-slate-700"
                >
                    <input
                        type="radio"
                        value="Influencer"
                        v-model="selectedRadio"
                        class="w-4 h-4 text-indigo-600"
                    />
                    <span>Influencer</span>
                </label>

                <label
                    class="flex items-center gap-2 cursor-pointer text-sm font-bold text-slate-700"
                >
                    <input
                        type="radio"
                        value="Foreign-visitors"
                        v-model="selectedRadio"
                        class="w-4 h-4 text-indigo-600"
                    />
                    <span>Foreign Visitors</span>
                </label>
            </div>
        </div>
    </div>
</template>
