<template>
  <div class="conversation-container d-flex">
    <setting-nav></setting-nav>
    <router-view :owner="owner"></router-view>
  </div>
</template>

<script setup>
import SettingNav from '@/pages/user/settings/SettingNav.vue'
import { defineProps } from 'vue'
defineProps({
  owner: {},
})
</script>

<style scoped>
.conversation-container {
  height: 92%;
}
</style>
