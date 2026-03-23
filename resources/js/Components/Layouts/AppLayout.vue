<script setup>
import { Link } from "@inertiajs/vue3";
import LanguageSwitcher from "@/Components/Shared/LanguageSwitcher.vue";
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <nav
            class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center shadow-sm"
        >
            <div class="text-xl font-bold text-blue-900 uppercase">
                ACAGMS | Adama City
            </div>
            <div class="flex items-center space-x-6">
                <LanguageSwitcher />
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-gray-600 hover:text-red-600 font-medium"
                    >Logout</Link
                >
            </div>
        </nav>

        <main class="flex-grow p-8">
            <slot />
        </main>

        <footer class="bg-white border-t p-4 text-center text-sm text-gray-500">
            &copy; 2026 Adama City Administration - Gallery Management System
        </footer>
    </div>
</template>
