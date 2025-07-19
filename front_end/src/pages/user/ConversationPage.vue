<template>
  <div class="conversation-container d-flex" v-if="conversations && thumbs && currentMessages">
    <chat-nav
      :thumbs="thumbs"
      :currentMessages="currentMessages"
      :owner="owner"
      :conversations="conversations"
    ></chat-nav>
    <div
      v-if="$route.name === 'conversation'"
      class="d-flex justify-content-center align-items-center"
      style="width: 100%; height: 100%"
    >
      Chọn bạn bè để bắt đầu cuộc trò chuyện
    </div>

    <router-view :owner="owner" :thumbs="thumbs" @updateConversation="updateConversation"></router-view>
  </div>
</template>

<script setup>
import ChatNav from '@/pages/user/conversations/ChatNav.vue'
import axios from 'axios'
import { onMounted, ref, defineProps, getCurrentInstance, watchEffect } from 'vue'
const { proxy } = getCurrentInstance()
const props = defineProps({
  owner: {},
})

const conversations = ref([])
const thumbs = ref([])
const currentMessages = ref([])

onMounted(async () => {
  try {
    const res = await axios.get('/conversation/get')
    conversations.value = res.data.conversations
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }

  // Lắng nghe tin nhắn mới
  window.Echo.private(`user.${props.owner.id}`)
    .listen('.conversation.received', (e) => {
      conversations.value = e.conversations
    })
    .error((error) => {
      console.error('Echo error:', error)
    })

  window.Echo.private(`user.${props.owner.id}`)
    .listen('.conversation.seender', (e) => {
      conversations.value = e.conversations
    })
    .error((error) => {
      console.error('Echo error:', error)
    })
})

watchEffect(() => {
  // Lấy ra thông tin cuộc trò chuyện
  conversations.value.forEach((conversation) => {
    // Lấy ảnh nền
    if (conversation.thumb) {
      thumbs.value[conversation.id] = proxy.$backendBaseUrl + conversation.thumb
    } else if (conversation.user.avatar) {
      thumbs.value[conversation.id] = proxy.$backendBaseUrl + conversation.user.avatar
    } else thumbs.value[conversation.id] = require('@/assets/images/avatar/default.jpg')

    // Lấy tin nhắn gần nhất
    if (conversation.message == null) {
      currentMessages.value[conversation.id] = 'Chưa có tin nhắn'
    } else if (
      conversation.message.type == 'message' ||
      conversation.message.type == 'video' ||
      conversation.message.type == 'call'
    ) {
      currentMessages.value[conversation.id] = conversation.message.content
    } else if (conversation.message.type == 'media') {
      currentMessages.value[conversation.id] = `Đã gửi phương tiện`
    }
  })
})
</script>

<style scoped>
.conversation-container {
  height: 92%;
}
</style>
