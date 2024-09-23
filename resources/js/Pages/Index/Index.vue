<template>
    <div>Index</div>
    <Link href="/hello">show page</Link>
    <div>The message is {{ message }}</div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import MainLayout from "../../Layouts/MainLayout.vue";

defineProps({
    message: String,
});
// setInterval(() => counter.value++, 1000);
</script>
