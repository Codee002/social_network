<template>
  <div style="width: 100%; height: 100%" class="d-flex justify-content-center align-items-center" v-if="loading">
    <div class="spinner-border" style=""></div>
  </div>
  <div v-else style="width: 100%">
    <div class="chat-area" v-if="conversation">
      <div class="chat-header">
        <div class="d-flex align-items-center">
          <img class="avatar" :src="thumbs[conversation.id]" alt="" />
          <div class="info">
            <p class="name">{{ converInfo.name }}</p>
            <!-- <p class="time-active">Hoat dong 1h truoc</p> -->
          </div>
        </div>
        <div class="chat-option">
          <i @click="startCall(conversation.id)" class="fa-solid fa-phone"></i>
          <i @click="startCall(conversation.id)" class="fa-solid fa-video"></i>
          <i class="fa-solid fa-circle-info"></i>
        </div>
      </div>
      <div class="chat-content" v-if="conversation.messages.length">
        <div v-for="(message, index) in conversation.messages" :key="index">
          <!-- Thời gian -->
          <div class="chat-time" v-if="getDisplayTime(index) != ''">{{ getDisplayTime(index) }}</div>

          <!-- Người khác -->
          <div class="is-user" v-if="message.user_id != owner.id">
            <router-link :to="{ name: 'profile', params: { user_id: message.user_id } }">
              <div class="chat-avatar">
                <img
                  class="avatar"
                  :src="
                    message.user.profile.avatar
                      ? $backendBaseUrl + message.user.profile.avatar
                      : require('@/assets/images/avatar/default.jpg')
                  "
                  alt=""
                />
              </div>
            </router-link>

            <div class="" style="width: 100%">
              <div v-if="conversation.type == 'group'" class="text-start mt-2 ps-2">
                <span style="font-size: 0.8rem; font-weight: 500">
                  {{ message.userName }}
                </span>
              </div>

              <div v-if="message.deleted_at == null">
                <!-- Ảnh và video -->
                <div
                  v-if="message.medias.length != 0"
                  class="d-flex flex-wrap align-items-end"
                  :class="{ 'mt-2': conversation.type == 'friend' }"
                  style="width: 60%"
                >
                  <div v-for="(media, index) in message.medias" :key="index" class="chat-message__media">
                    <a :href="$backendBaseUrl + media.path">
                      <img v-if="media.type == 'image'" :src="$backendBaseUrl + media.path" />
                      <video v-else-if="media.type == 'video'" :src="$backendBaseUrl + media.path" controls />
                    </a>
                  </div>
                </div>

                <!-- Tin nhắn bình thưong -->
                <div class="chat-message" v-if="message.content != null">
                  <div :class="{ type__video: message.type == 'video' || message.type == 'call' }">
                    <i v-if="message.type == 'call'" class="fa-solid fa-phone"></i>
                    <i v-if="message.type == 'video'" class="fa-solid fa-video"></i>
                    <p class="d-contents mb-0">
                      {{ message.content }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Tin nhắn bị thu hồi -->
              <div class="chat-message" v-if="message.deleted_at != null">
                <div>
                  <i class="d-contents mb-0">Tin nhắn đã bị thu hồi</i>
                </div>
              </div>
            </div>
          </div>

          <!-- Người dùng hiện tại và tin nhắn k thu hồi -->
          <div class="is-owner" v-if="message.user_id == owner.id">
            <div class="d-flex flex-column align-items-end chat-message__container" v-if="message.deleted_at == null">
              <i
                data-bs-toggle="modal"
                :data-bs-target="'#modal__remove__message' + message.id"
                class="fa-solid fa-xmark remove-message"
              ></i>

              <!-- Ảnh và video -->
              <div
                v-if="message.medias.length != 0"
                class="mt-2 d-flex flex-wrap align-items-end justify-content-end"
                style="width: 60%"
              >
                <div v-for="(media, index) in message.medias" :key="index" class="chat-message__media">
                  <a :href="$backendBaseUrl + media.path">
                    <img v-if="media.type == 'image'" :src="$backendBaseUrl + media.path" />
                    <video v-else-if="media.type == 'video'" :src="$backendBaseUrl + media.path" controls />
                  </a>
                </div>
              </div>

              <!-- Tin nhắn -->
              <div style="width: 100%" class="d-flex justify-content-end chat-message-container">
                <div class="chat-message" v-if="message.content != null">
                  <div :class="{ type__video: message.type == 'video' || message.type == 'call' }">
                    <i v-if="message.type == 'call'" class="fa-solid fa-phone"></i>
                    <i v-if="message.type == 'video'" class="fa-solid fa-video"></i>
                    <p class="d-contents mb-0">
                      {{ message.content }}
                    </p>
                  </div>
                </div>

                <!-- Modal tin nhắn -->
                <div
                  class="modal"
                  :id="'modal__remove__message' + message.id"
                  tabindex="-1"
                  aria-labelledby="modal-label"
                  aria-hidden="true"
                >
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="modal-label">Thu hồi tin nhắn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-2 mt-2">
                          <p>
                            Bạn chắc chắn thu hồi tin nhắn?
                            <b></b>
                          </p>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="upload__btn btn upload__btn--secondary" data-bs-dismiss="modal">
                          Hủy
                        </button>
                        <button
                          type="submit"
                          class="upload__btn btn btn-danger"
                          @click="removeMessage(message.id)"
                          data-bs-dismiss="modal"
                        >
                          Thu hồi tin nhắn
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Hiển thị tiến trình -->
              <div v-if="uploadProgress > 0 && uploadProgress < 100" class="upload-bar">
                <div class="progress" :style="{ width: uploadProgress + '%' }"></div>
                <span>Đang upload: {{ uploadProgress }}%</span>
              </div>
            </div>

            <div v-else-if="message.deleted_at != null">
              <div class="chat-message">
                <div>
                  <i class="d-contents mb-0">Tin nhắn đã bị thu hồi</i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="chat-content justify-content-center" v-else>
        <p>Chưa có tin nhắn nào</p>
      </div>
      <div class="chat-form">
        <!-- Xem trước ảnh, video gửi đi -->
        <div v-if="mediaFiles.length" class="d-flex flex-wrap align-items-end" style="width: 100%; padding: 0.4rem">
          <div
            v-for="(file, index) in mediaFiles"
            :key="index"
            class="media-item media-preview"
            style="margin-bottom: 10px"
          >
            <i @click="removeFile(index)" class="fa-solid fa-xmark media__deleteBtn"></i>
            <img v-if="file.type.startsWith('image/')" :src="file.url" />
            <video v-else-if="file.type.startsWith('video/')" :src="file.url" />
          </div>
        </div>
        <form @submit.prevent="sendMessage()" action="" style="">
          <!-- Form gửi tin nhắn -->
          <textarea
            type="text"
            class="form-control"
            @input="autoResize"
            rows="1"
            placeholder="Tin nhắn..."
            v-model="content"
          ></textarea>
          <div class="icon d-flex">
            <i @click="triggerFileInput" class="fa-solid fa-image"></i>
            <button type="submit" style="background: none; border: none">
              <i class="fa-solid fa-paper-plane"></i>
            </button>
            <input
              type="file"
              ref="fileInput"
              multiple
              accept="image/*,video/*"
              @change="handleFilesChange"
              style="display: none"
            />
          </div>
        </form>
      </div>
    </div>
  </div>
</template>


<script setup>
import axios from 'axios'
import { computed, onMounted, ref, watch, defineProps } from 'vue'
import { useRoute } from 'vue-router'
import { differenceInMinutes, format } from 'date-fns'
import { useToast } from 'vue-toastification'
// import AgoraRTC from 'agora-rtc-sdk-ng'

const props = defineProps({
  thumbs: {},
  owner: {},
})

const toast = useToast()
const content = ref('')
const route = useRoute()
const conversationId = computed(() => route.params.conversation_id)
const conversation = ref()
const loading = ref(true)
const converInfo = computed(() => getConverInfo())
const fileInput = ref()
const mediaFiles = ref([])
const uploadProgress = ref(0)

onMounted(async () => {
  try {
    await fetchMessages()
    scrollToBottom()
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }

  // Nhận tin nhắn
  window.Echo.private(`user.${props.owner.id}`)
    .listen('.conversation.received', (e) => {
      if (e.message.conversation_id == conversationId.value) {
        conversation.value.messages.unshift(e.message)
      }
    })
    .error((error) => {
      console.error('Echo error:', error)
    })

  // Gửi tin nhắn
  window.Echo.private(`user.${props.owner.id}`)
    .listen('.conversation.sender', (e) => {
      if (e.message.conversation_id == conversationId.value) {
        conversation.value.messages.unshift(e.message)
      }
    })
    .error((error) => {
      console.error('Echo error:', error)
    })

  // Nhận tin nhắn bị thu hồi
  window.Echo.private(`user.${props.owner.id}`)
    .listen('.conversation.received.remove', (e) => {
      if (e.message.conversation_id == conversationId.value) {
        conversation.value.messages.find((message) => message.id == e.message.id).deleted_at = e.message.deleted_at
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

// Lấy thông tin cuộc trò chuyện
function getConverInfo() {
  let avatar = ''
  let name = ''
  if (conversation.value.type == 'friend') {
    let users = conversation.value.users.filter((user) => user.id != props.owner.id)
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

// Lấy tin nhắn
async function fetchMessages() {
  try {
    let res = await axios.get(`/conversation/message/${conversationId.value}`)
    conversation.value = res.data.conversation
    console.log(conversation.value)
    loading.value = false
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }
}

// Gửi tin nhắn
async function sendMessage() {
  if (mediaFiles.value.length == 0 && content.value.trim().length == 0) return

  try {
    const formData = new FormData()
    formData.append('content', content.value)
    formData.append('user_id', props.owner.id)
    formData.append('conversation_id', conversation.value.id)

    // Nếu có media
    if (mediaFiles.value.length != 0) {
      formData.append('type', 'media')
      mediaFiles.value.forEach((media) => {
        formData.append('media[]', media.file)
      })
    } else {
      formData.append('type', 'message')
    }

    content.value = ''
    mediaFiles.value = []
    await axios.post(`conversation/sendMessage`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      onUploadProgress: (e) => {
        const percent = Math.round((e.loaded * 100) / e.total)
        uploadProgress.value = percent
      },
    })
    uploadProgress.value = 0
  } catch (error) {
    console.log('Gửi tin nhắn thất bại!', error)
  }
}

//  Lấy thời gian nhắn
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

// -------------------- Gửi ảnh --------------------
const triggerFileInput = () => {
  fileInput.value.click()
}

// Xử lý phương tiện
const handleFilesChange = (event) => {
  const files = Array.from(event.target.files)
  files.forEach((file) => {
    const url = URL.createObjectURL(file)
    mediaFiles.value.push({
      file,
      url,
      type: file.type,
    })
    console.log(file.type)
  })
}

// Xóa ảnh
const removeFile = (index) => {
  mediaFiles.value.splice(index, 1)
}
// ------------------------------------------------------------

function scrollToBottom() {
  const chatArea = document.querySelector('.chat-area')
  if (chatArea) {
    chatArea.scrollTop = chatArea.scrollHeight
  }
}

function autoResize(event) {
  const textarea = event.target
  textarea.style.height = 'auto'
  textarea.style.height = textarea.scrollHeight + 'px'
}

// -------------------- Gọi --------------------
async function startCall(conversationId) {
  console.log('call')

  try {
    let content = 'Đã bắt đầu cuộc gọi'

    let res = await axios.post(`conversation/startCall`, {
      conversation_id: conversationId,
      content: content,
      type: 'video',
    })

    const url = `/call-window?channel=${encodeURIComponent(res.data.channel)}&token=${encodeURIComponent(
      res.data.token
    )}&message=${res.data.message.id}&uid=${res.data.uid}&role=caller&thumb=${res.data.thumb}`
    window.open(url, '_blank', 'width=800,height=600')
  } catch (error) {
    console.log(error)
  }
}

// --------------- Thu hồi tin nhắn ----------
async function removeMessage(messageId) {
  console.log('REMOVE TIN NHAN ', messageId)
  try {
    let res = await axios.post(`/conversation/removeMessage`, {
      message_id: messageId,
    })
    toast.success(res.data.message, {
      position: 'bottom-right',
    })
    const messageDeleted = res.data.result
    conversation.value.messages.find((message) => message.id == messageDeleted.id).deleted_at =
      messageDeleted.deleted_at
    console.log(res.data.result)
  } catch (error) {
    console.log('Thu hồi tin nhắn thất bại!', error)
    toast.error(error.response.data.message, {
      position: 'bottom-right',
    })
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
  align-items: end;
}

.chat-content .is-owner {
  width: 100%;
  /* border: 0.01rem solid #333; */
  display: flex;
  justify-content: end;
  padding: 0.1rem;
  align-items: end;
  position: relative;
}

.chat-content .chat-time {
  font-weight: 400;
  font-size: 0.7rem;
  margin: 0.8rem 0;
}

.chat-content .chat-message {
  background-color: var(--extra-bg);
  padding: 0.4rem 1rem;
  border-radius: 1.2rem;
  max-width: 35rem;
  word-wrap: break-word;
  text-align: left;
  width: fit-content;
  position: relative;
}

.chat-content .is-owner .chat-message {
  background-color: var(--main1-color);
}

.chat-message__container {
  position: relative;
  width: 100%;
}

.chat-message .type__video {
  padding: 0.5rem;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  /* flex-direction: column; */
}

.chat-message .type__video i {
  font-size: 1.2rem;
  margin-right: 1rem;
}

.chat-content img {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  margin-right: 0.4rem;
}

.is-owner .remove-message {
  display: none;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  right: -2rem;
  /* right: 23rem; */
  font-size: 1.2rem;
  cursor: pointer;
}

.is-owner:hover .remove-message {
  display: block;
}

.is-owner:hover .chat-message__container {
  right: 3rem;
}

.chat-message p {
  display: contents;
  margin: 0;
}

.chat-message__media {
  /* width: 10rem; */
  border-radius: 0.5rem;
}

.chat-message__media img {
  width: 10rem;
  border-radius: 0;
  height: unset;
  border-radius: 1.2rem;
  margin-bottom: 0.2rem;
}

.chat-message__media video {
  width: 100%;
  border-radius: 0;
  height: unset;
  border-radius: 1.2rem;
}

.chat-form {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
  border-radius: 1.4rem;
  padding: 0.2rem 0.4rem;
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
.chat-form textarea {
  width: 90% !important;
  /* background-color: var(--main-bg) !important; */
  border-radius: 2rem !important;
  background: transparent !important;
  border: none !important;
  outline: none !important;
}

.chat-form textarea:focus {
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

.media-preview {
  position: relative;
  border-radius: 0.5rem;
  width: 7rem;
}

.media-preview .media__deleteBtn {
  position: absolute;
  right: 0.4rem;
  top: 0.4rem;
  font-size: 1.5rem;
  cursor: pointer;
  z-index: 10;
}

.media-preview img,
.media-preview video {
  width: 100%;
  border-radius: 0.5rem;
}

.upload-bar {
  position: relative;
  background: #eee;
  height: 10px;
  border-radius: 6px;
  margin-top: 10px;
  margin-bottom: 10px;
}
.progress {
  background: var(--main1-color);
  height: 100%;
  border-radius: 6px;
  transition: width 0.3s;
}
</style>
