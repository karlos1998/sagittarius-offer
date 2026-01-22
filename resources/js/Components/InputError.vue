<script setup>
defineProps({
    message: String,
});
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ message }}
        </p>
    </div>
</template>
