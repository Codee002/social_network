<template>
  <input
    type="text"
    :name="name"
    :placeholder="placeholder"
    class="form-control"
  />
</template>

<script setup>
import { defineProps } from "vue";
defineProps({
  name: {
    type: String,
    required: true,
  },
  placeholder: {
    type: String,
    required: true,
  },
});
</script>


<style scoped>
input {
  border: unset !important;
  height: 70%;
  border-radius: 2rem !important;
  padding: 1rem;
  width: 17rem;
  background-color: var(--extra-bg) !important;
}
</style>
