<template>
  <div style="width: 100%; height: 100%" class="d-flex justify-content-center align-items-center" v-if="loading">
    <div class="spinner-border" style=""></div>
  </div>
  <div v-else style="width: 100%">
    <div class="chat-area" v-if="conversation">
      <div class="chat-header">
        <div class="d-flex align-items-center">
          <img class="avatar" :src="converInfo.avatar" alt="" />
          <div class="info">
            <p class="name">{{ converInfo.name }}</p>
            <p class="time-active">Hoat dong 1h truoc</p>
          </div>
        </div>
        <div class="chat-option">
          <i class="fa-solid fa-phone"></i>
          <i class="fa-solid fa-video"></i>
          <i class="fa-solid fa-circle-info"></i>
        </div>
      </div>
      <div class="chat-content" v-if="conversation.messages.length">
        <div v-for="(message, index) in conversation.messages" :key="index">
          <div class="chat-time">{{ getDisplayTime(index) }}</div>
          <div class="is-user" v-if="message.user_id != owner.id">
            <router-link :to="{ name: 'profile', params: { user_id: message.user_id } }">
              <div class="chat-avatar">
                <img class="avatar" :src="srcAvtUser" alt="" />
              </div>
            </router-link>
            <div class="chat-message">{{ message.content }}</div>
          </div>
          <div class="is-owner" v-if="message.user_id == owner.id">
            <div class="chat-message">{{ message.content }}</div>
          </div>
        </div>
      </div>
      <div class="chat-content justify-content-center" v-else>
        <p>Chưa có tin nhắn nào</p>
      </div>
      <div class="chat-form">
        <form @submit.prevent="sendMessage()" action="" style="">
          <input type="text" class="form-control" placeholder="Tin nhắn..." v-model="content" />
          <div class="icon">
            <i class="fa-solid fa-image"></i>
            <button type="submit" style="background: none; border: none">
              <i class="fa-solid fa-paper-plane"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>


<script setup>
import auth from '@/utils/auth'
import axios from 'axios'
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { differenceInMinutes, format } from 'date-fns'

const content = ref()
const route = useRoute()
const conversationId = computed(() => route.params.conversation_id)
const conversation = ref()
const owner = ref()
const loading = ref(true)
const converInfo = computed(() => getConverInfo())

onMounted(async () => {
  try {
    owner.value = await auth.getOwner()
    await fetchMessages()
    scrollToBottom()
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }

  window.Echo.private(`user.${owner.value.id}`)
    .listen('.conversation.received', (e) => {
      if (e.message.conversation_id == conversationId.value) {
        conversation.value.messages.unshift(e.message)
      }
    })
    .error((error) => {
      console.error('Echo error:', error)
    })

  window.Echo.private(`user.${owner.value.id}`)
    .listen('.conversation.sender', (e) => {
      if (e.message.conversation_id == conversationId.value) {
        conversation.value.messages.unshift(e.message)
      }
    })
    .error((error) => {
      console.error('Echo error:', error)
    })
})

watch(conversationId, async (newId) => {
  loading.value = true
  if (newId) await fetchMessages()
})

const srcAvtUser = computed(() => {
  return require('@/assets/images/avatar/default.jpg')
})

function getConverInfo() {
  let avatar = ''
  let name = ''
  if (conversation.value.type == 'friend') {
    let users = conversation.value.users.filter((user) => user.id != owner.value.id)
    avatar = users[0].profile.avatar ?? require('@/assets/images/avatar/default.jpg')
    name = users[0].profile.name
  } else {
    avatar = conversation.value.thumb
    name = conversation.value.name
  }
  return {
    avatar: avatar,
    name: name,
  }
}

async function fetchMessages() {
  try {
    let res = await axios.get(`/conversation/message/${conversationId.value}`)
    conversation.value = res.data.conversation
    loading.value = false
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }
}

async function sendMessage() {
  if (content.value.trim().length != 0) {
    try {
      let data = content.value
      content.value = ''
      await axios.post(`conversation/sendMessage`, {
        user_id: owner.value.id,
        conversation_id: conversation.value.id,
        content: data,
        type: 'message',
      })
    } catch (error) {
      console.log('Gửi tin nhắn thất bại!', error)
    }
  }
}

function getDisplayTime(messIndex) {
  const createdAt = new Date(conversation.value.messages[messIndex].created_at)
  if (messIndex === conversation.value.messages.length - 1) {
    return format(createdAt, 'MMM d, yyyy, h:mm a')
  }
  const currentTime = new Date(conversation.value.messages[messIndex].created_at)
  const prevTime = new Date(conversation.value.messages[messIndex + 1].created_at)
  const diffMinutes = differenceInMinutes(currentTime, prevTime)
  return diffMinutes > 20 ? format(currentTime, 'MMM d, yyyy, h:mm a') : ''
}

function scrollToBottom() {
  const chatArea = document.querySelector('.chat-area')
  if (chatArea) {
    chatArea.scrollTop = chatArea.scrollHeight
  }
}
</script>

<style scoped>
.chat-area {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
  text-align: center;
  width: 100%;
  height: 100%;
}
.chat-header {
  display: flex;
  justify-content: space-between;
  border: 0.01rem solid #333;
  height: 10%;
  width: 100%;
  align-items: center;
  padding: 0 1.4rem;
}

.chat-header .avatar {
  width: 2.4rem;
  height: 2.4rem;
  border-radius: 50%;
  margin-right: 0.8rem;
}

.chat-header .info {
  display: flex;
  flex-direction: column;
  justify-content: start;
}

.chat-header .info p {
  margin: 0;
  padding: 0;
  text-align: left;
}

.chat-header .info .name {
  font-weight: 650;
}

.chat-header .info .time-active {
  font-weight: 350;
  font-size: 0.8rem;
}

.chat-option {
  display: flex;
}

.chat-option i {
  margin: 0 0.6rem;
  font-size: 1.4rem;
  color: var(--main1-color);
  cursor: pointer;
}

.chat-content {
  display: flex;
  flex-direction: column-reverse;
  overflow-y: auto;
  position: relative;
  width: 100%;
  height: 100%;
  padding: 0.4rem 1rem;
}

.chat-content .is-user {
  width: 100%;
  /* border: 0.01rem solid #333; */
  display: flex;
  justify-content: start;
  padding: 0.1rem;
  align-items: center;
}

.chat-content .is-owner {
  width: 100%;
  /* border: 0.01rem solid #333; */
  display: flex;
  justify-content: end;
  padding: 0.1rem;
  align-items: center;
}

.chat-content .chat-time {
  font-weight: 400;
  font-size: 0.7rem;
  /* margin: 0.8rem 0; */
}

.chat-content .chat-message {
  background-color: var(--extra-bg);
  padding: 0.4rem 1rem;
  border-radius: 1.2rem;
  max-width: 35rem;
  word-wrap: break-word;
  text-align: left;
}

.chat-content .is-owner .chat-message {
  background-color: var(--main1-color);
}

.chat-content img {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  margin-right: 0.4rem;
}

.chat-form {
  display: flex;
  justify-content: space-between;
  height: 3rem;
  align-items: center;
  border-radius: 4rem;
  padding: 1rem 0.4rem;
  width: 90%;
  background-color: var(--main-bg) !important;
  border: 1px solid var(--border-color) !important;
  color: var(--font-color) !important;
  margin-bottom: 0.6rem;
  margin-top: 1rem;
}

.chat-form form {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.chat-form input {
  width: 90% !important;
  /* background-color: var(--main-bg) !important; */
  border-radius: 2rem !important;
  background: transparent !important;
  border: none !important;
  outline: none !important;
}

.chat-form input:focus {
  box-shadow: none !important;
  background-color: transparent !important;
  color: var(--font-color) !important;
}

.chat-form .icon i {
  margin: 0 0.6rem;
  font-size: 1.2rem;
  font-weight: 650;
  cursor: pointer;
}

.chat-form .icon i:hover {
  color: var(--main1-color);
}
</style>
