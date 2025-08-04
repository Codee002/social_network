<template>
  <div class="post-card">
    <div>
      <div class="post__media">
        <div class="p-4 d-flex flex-column align-items-center justify-content-center">
          <i class="fa-solid fa-ban text-danger" style="font-size: 15rem"></i>
          <h6 class="text-center mt-3">Xin chào {{ owner.profile.name }}</h6>
          <h6 class="text-center">Vì 1 số lý do mà tài khoản của bạn đã bị khóa</h6>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
//--------------------------------------------------------
import { defineProps } from 'vue'
defineProps({
  owner:{},
})
</script>

<style scoped>
.post-card {
  background-color: var(--main-bg);
  border-radius: 0.75rem;
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.1rem 0.1rem;
}

.post__media {
  position: relative;
}
</style>
