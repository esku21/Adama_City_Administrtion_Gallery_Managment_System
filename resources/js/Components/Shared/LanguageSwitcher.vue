<script setup>
const locales = [
    { name: "Oromoo", code: "om" },
    { name: "Amharic", code: "am" },
    { name: "English", code: "en" },
];
</script>

<template>
    <div class="flex items-center space-x-2">
        <button
            v-for="lang in locales"
            :key="lang.code"
            class="px-2 py-1 text-[10px] font-bold border rounded border-gray-300 hover:bg-blue-900 hover:text-white uppercase transition"
        >
            {{ lang.code }}
        </button>
    </div>
</template>
