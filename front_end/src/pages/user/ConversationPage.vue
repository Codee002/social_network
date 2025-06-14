<template>
  <div class="conversation-container d-flex" v-if="conversations">
    <chat-nav :conversations="conversations"></chat-nav>

    <div
      v-if="$route.name === 'conversation'"
      class="d-flex justify-content-center align-items-center"
      style="width: 100%; height: 100%"
    >
      Chọn bạn bè để bắt đầu cuộc trò chuyện
    </div>

    <router-view></router-view>
  </div>
</template>

<script setup>
import ChatNav from '@/pages/user/conversations/ChatNav.vue'
import axios from 'axios'
import { onMounted, ref } from 'vue'

const conversations = ref()

onMounted(async () => {
  try {
    const res = await axios.get('/conversation/get')
    conversations.value = res.data.conversations
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }
})
</script>

<style scoped>
.conversation-container {
  height: 92%;
}
</style>
