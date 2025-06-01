<template>
  <div class="d-flex nav-group">
    <slot name="icon"></slot>
    <slot name="des"></slot>
  </div>
</template>

<style scoped>
.nav-group {
  width: 100%;
  height: 100%;
  justify-content: start;
  align-items: center;
  padding: 1rem;
  border-radius: 0.8rem;
  font-weight: 500;
  font-size: 1.1rem;
}

.nav-group :deep(i) {
  font-size: 1.2rem;
  margin-right: 1rem;
}

.nav-group :deep(img) {
  width: 13%;
  margin-right: 1rem;
}

.nav-group:hover {
  background-color: var(--extra-bg);
}
</style>
