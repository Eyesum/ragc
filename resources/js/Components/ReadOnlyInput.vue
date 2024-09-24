<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<Number>();

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
});

</script>

<template>
    <input
        class="border-gray-300 bg-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-500 rounded-md shadow-sm"
        v-model="model"
        :disabled = "true"
    />
</template>
