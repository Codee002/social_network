<template>
  <div class="nav" :class="isActive">
    <div class="icon-container">
      <slot></slot>
    </div>
  </div>
</template>


<script setup>
import { defineProps } from "vue";
defineProps({
  isActive: {
    required: false,
  },
});

</script>

<style scoped>
.nav {
  display: flex;
  height: 100%;
  align-content: center;
  cursor: pointer;
  border-bottom: 0.25rem solid rgba(0, 0, 0, 0);
  width: 5.5rem;
  justify-content: center;
  height: 3.5rem;
  padding: 0.3rem 0rem;
}

.nav.is-active {
  border-bottom-color: var(--main1-color);
}

.nav.is-active :deep(i),
.nav.is-active :deep(p) {
  color: var(--main1-color);
}

.icon-container {
  display: flex;
  height: 100%;
  border-radius: 1rem;
  align-items: center;
  /* padding: 1rem; */
  width: 90%;
  justify-content: center;
  align-items: center;
}

.icon-container:hover {
  background-color: var(--extra-bg);
}

.nav :deep(i) {
  font-size: 1.2rem !important;
  color: unser !important;
}

.nav :deep(p) {
  display: contents;
  font-size: 1rem !important;
}
</style>
