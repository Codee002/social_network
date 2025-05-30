<template>
  <div class="custom-toast">
    {{ message }}
    <!-- <button @click="$emit('close-toast')">Đóng</button> -->
  </div>
</template>

<script setup>
import { defineProps } from "vue";
defineProps({
  message: {
    type: String,
    required: true, 
  },
});

</script>

