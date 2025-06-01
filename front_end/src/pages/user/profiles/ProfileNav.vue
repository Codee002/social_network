<template>
  <div class="profile__nav" data-nav-fixed="false">
    <icon-nav :isActive="{ 'is-active': mode === 'all' }" @click="changeMode('all')"> <p>Tất cả</p></icon-nav>

    <icon-nav :isActive="{ 'is-active': mode === 'intro' }" @click="changeMode('intro')"> <p>Giới thiệu</p></icon-nav>

    <icon-nav :isActive="{ 'is-active': mode === 'friend' }" @click="changeMode('friend')"> <p>Bạn bè</p></icon-nav>

    <icon-nav :isActive="{ 'is-active': mode === 'story' }" @click="changeMode('story')"> <p>Story</p></icon-nav>

    <icon-nav :isActive="{ 'is-active': mode === 'image' }" @click="changeMode('image')"> <p>Hình ảnh</p></icon-nav>

    <icon-nav :isActive="{ 'is-active': mode === 'video' }" @click="changeMode('video')"> <p>Video</p></icon-nav>
  </div>
</template>

<script setup>
import IconNav from "@/layouts/partials/IconNav.vue";
import { defineEmits, defineProps } from "vue";

const emit = defineEmits(["changeMode"]);

defineProps({
  mode: {
    Type: String,
  }
})

function changeMode(mode) {
  emit("changeMode", mode);
}
</script>

<style scoped>
.profile__nav {
  position: relative;
  display: flex;
  margin-inline: 2rem;
  border-top: var(--extra-bg) solid 0.1rem;
  height: calc(var(--header-heigh) * 1.1);
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.1rem 0.1rem;
}

.profile__nav__tab {
  height: 100%;
  align-content: center;
  text-align: center;
  border-top: 0.25rem solid rgba(0, 0, 0, 0);
  border-bottom: 0.25rem solid rgba(0, 0, 0, 0);
}
</style>
