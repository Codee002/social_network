<template>
  <div style="height: 100%; width: 100%">
    <!-- Khi chưa chấp nhận -->
    <div
      v-if="state === 'waiting' && role === 'receiver'"
      class="text-center mt-8 d-flex justify-content-center align-items-center flex-column"
      style="height: 100%; width: 100%"
    >
      <img style="width: 30%; border-radius: 3rem" :src="image" alt="" />
      <p class="mt-4 fs-5 fw-bolder">Bạn có cuộc gọi</p>
      <div class="call__option">
        <button data-bs-dismiss="modal" type="button" class="upload__btn btn upload__btn--secondary">Từ chối</button>
        <button type="submit" @click="acceptCall" class="upload__btn btn upload__btn--primary">Chấp nhận</button>
      </div>
    </div>

    <div
      v-if="state === 'waiting' && role === 'caller'"
      class="text-center mt-8 d-flex justify-content-center align-items-center flex-column"
      style="height: 100%; width: 100%"
    >
      <img style="width: 30%; border-radius: 3rem" :src="image" alt="" />
      <p class="mt-4 fs-5 fw-bolder">Đang chờ đối phương chấp nhận</p>
    </div>

    <!-- Khi đã chấp nhận -->
    <div v-if="state === 'accepted'" class="meet__container row">
      <div class="col-lg-6 col-sm-12 p-3 d-flex flex-column justify-content-center align-items-center">
        <p>Bạn</p>
        <div ref="localRef" class="user__camera">
          <img
            v-if="isCamMuted == true"
            :src="
              owner.profile.avatar
                ? $backendBaseUrl + owner.profile.avatar
                : require('@/assets/images/avatar/default.jpg')
            "
            alt=""
            class="user__avatar"
          />
        </div>
      </div>
      <div
        v-for="(user, index) in remoteUsers"
        :key="index"
        class="col-lg-6 col-sm-12 p-3 d-flex flex-column justify-content-center align-items-center"
      >
        <p>{{ profileRemoteUsers[user.uid].profile.name }}</p>
        <div ref="" class="user__camera" :id="`remote_user_${user.uid}`">
          <img
            v-if="profileRemoteUsers[user.uid].isCamMuted == true"
            :src="
              profileRemoteUsers[user.uid].profile.avatar
                ? $backendBaseUrl + profileRemoteUsers[user.uid].profile.avatar
                : require('@/assets/images/avatar/default.jpg')
            "
            alt=""
            class="user__avatar"
          />
        </div>
      </div>
      <div class="meet__action" v-if="state === 'accepted'">
        <i class="fa-solid fa-microphone" v-if="!isMicMuted" @click="toggleMic"></i>
        <i class="fa-solid fa-microphone-slash" v-else @click="toggleMic"></i>
        <i class="fa-solid fa-video" v-if="!isCamMuted" @click="toggleCamera"></i>
        <i class="fa-solid fa-video-slash" v-else @click="toggleCamera"></i>
        <i class="fa-solid fa-phone-slash" @click="leaveCall"></i>
      </div>
    </div>

    <!-- Khi đã kết thúc -->
    <div
      v-if="state === 'finished'"
      class="text-center mt-8 d-flex justify-content-center align-items-center flex-column"
      style="height: 100%; width: 100%"
    >
      <img style="width: 30%; border-radius: 3rem" :src="image" alt="" />
      <p class="mt-4 fs-5 fw-bolder">Cuộc gọi đã kết thúc</p>
    </div>
  </div>
</template>


<script setup>
import { useRoute } from 'vue-router'
import { computed, getCurrentInstance, onBeforeUnmount, onMounted, ref } from 'vue'
const { proxy } = getCurrentInstance()
import AgoraRTC from 'agora-rtc-sdk-ng'
AgoraRTC.setLogLevel(AgoraRTC.LOG_NONE)
import axios from 'axios'
import auth from '@/utils/auth'

// Lấy thông tin của cuộc gọi
const route = useRoute()
const channel = decodeURIComponent(route.query.channel ?? '')
const message = route.query.message
const token = decodeURIComponent(route.query.token ?? '')
const uid = Number(route.query.uid)
const role = route.query.role // caller | receiver
const state = ref('waiting') // waiting | accepted
const image = computed(() => {
  if (route.query.thumb != 'null') return proxy.$backendBaseUrl + route.query.thumb
  return require('@/assets/images/avatar/default.jpg')
})

const isMicMuted = ref(false)
const isCamMuted = ref(false)

const owner = ref({})
// Đợi người khác chấp nhận
if (role === 'caller') {
  window.Echo.private(`user.${uid}`).listen('.call.accept', (e) => {
    if (e.channel === channel) {
      console.log('Người nghe đã chấp nhận cuộc gọi!')
      state.value = 'accepted'
      joinAgora()
    }
  })
}

// Phía người dùng chấp nhận
onMounted(async () => {
  owner.value = await auth.getOwner()
  if (role === 'receiver') {
    if (route.query.accepted === '1') {
      state.value = 'accepted'
      await joinAgora()
    }
  }
})

const remoteUsers = ref([])
const profileRemoteUsers = ref([])

// Chấp nhận cuộc gọi
async function acceptCall() {
  try {
    const res = await axios.post('/conversation/acceptCall', { channel, message_id: message })

    window.location.href = `/call-window?channel=${encodeURIComponent(channel)}&token=${encodeURIComponent(
      res.data.token
    )}&uid=${res.data.uid}&role=receiver&accepted=1`
  } catch (error) {
    console.log(error)
  }
}

// Khi đã chấp nhận
const localRef = ref(null)
const client = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' })
let localTrack, audioTrack

async function joinAgora() {
  try {
    await navigator.mediaDevices.getUserMedia({ video: true, audio: true })
  } catch (error) {
    console.error('Không lấy được video và mic', error)
    alert('Không lấy được video và mic')
  }

  try {
    await client.join(process.env.VUE_APP_AGORA_APP_ID, channel, token, uid)

    // Xin tài nguyên
    try {
      localTrack = await AgoraRTC.createCameraVideoTrack()
      audioTrack = await AgoraRTC.createMicrophoneAudioTrack()
    } catch (e) {
      alert('Bạn cần cấp quyền camera và micro để gọi video')
    }

    if (localTrack) {
      localTrack.play(localRef.value)
      await client.publish([audioTrack, localTrack])
    }

    // Lắng nghe người dùng vào kênh
    client.on('user-published', async (user, mediaType) => {
      // Lấy thông tin user
      try {
        let res = await axios.get(`/user/${user.uid}`)
        profileRemoteUsers.value[user.uid] = res.data.user
        profileRemoteUsers.value[user.uid].isCamMuted = false

        if (!remoteUsers.value.some((u) => u.uid == user.uid)) {
          remoteUsers.value.push(user)
        }
        console.log(`User đã tham gia cuộc gọi`, profileRemoteUsers.value)
        console.log(`DS người dùng hiện tại`, remoteUsers.value)
      } catch (error) {
        console.log('Lấy thông tin thất bại', error)
      }

      // Join vào kênh
      await client.subscribe(user, mediaType)
      if (mediaType === 'video') user.videoTrack.play(document.getElementById(`remote_user_${user.uid}`))
      if (mediaType === 'audio') user.audioTrack.play()
    })

    // Lắng nghe khi người dùng khác bật tắt camera
    client.on('stream-message', (uid, data) => {
      const message = JSON.parse(new TextDecoder().decode(data))
      if (message.cameraMuted !== undefined) {
        profileRemoteUsers.value[uid].isCamMuted = message.cameraMuted
      }
    })

    // Lắng nghe người dùng rời kênh
    client.on('user-left', (user) => {
      remoteUsers.value = remoteUsers.value.filter((u) => u.uid != user.uid)
      console.log('Số người còn lại: ', remoteUsers.value)
      if (remoteUsers.value.length == 0) {
        endCall()
      }
    })
  } catch (error) {
    console.error('Kết nối agora thất bại:', error)
  }
}

// Kết thúc cuộc gọi
async function endCall() {
  try {
    if (localTrack) {
      localTrack.stop()
      localTrack.close()
    }
    if (audioTrack) {
      audioTrack.stop()
      audioTrack.close()
    }

    await client.leave()
    state.value = 'finished'
    remoteUsers.value = []
  } catch (e) {
    console.error('Lỗi khi kết thúc cuộc gọi:', e)
  }
}

// Rời cuộc gọi
async function leaveCall() {
  try {
    if (localTrack) {
      localTrack.stop()
      localTrack.close()
    }
    if (audioTrack) {
      audioTrack.stop()
      audioTrack.close()
    }

    await client.leave()
    console.log('Rời cuộc gọi thành công')
    window.close()
  } catch (e) {
    console.error('Lỗi khi rời cuộc gọi:', e)
  }
}

// Bật tắt mic
async function toggleMic() {
  if (audioTrack) {
    isMicMuted.value = !isMicMuted.value
    await audioTrack.setMuted(isMicMuted.value)
  } else {
    try {
      audioTrack = await AgoraRTC.createCameraVideoTrack()
    } catch (e) {
      alert('Bạn cần cấp quyền micro hoặc micro đã được sử dụng')
      console.log('Có lỗi khi thao tác mic', e)
    }
  }
}

// Bật tắt cam
async function toggleCamera() {
  if (localTrack) {
    isCamMuted.value = !isCamMuted.value
    await localTrack.setMuted(isCamMuted.value)

    // Gửi thông điệp trạng thái camera
    const message = JSON.stringify({ cameraMuted: isCamMuted.value })
    await client.sendStreamMessage(new TextEncoder().encode(message))
  } else {
    try {
      localTrack = await AgoraRTC.createCameraVideoTrack()
    } catch (e) {
      alert('Bạn cần cấp quyền video hoặc video đã được sử dụng')
      console.log('Có lỗi khi thao tác video', e)
    }
  }
}

onBeforeUnmount(async () => {
  await leaveCall()
})
</script>

<style scoped>
.call__option {
  display: flex;
  gap: 2rem;
  width: 50%;
  position: relative;
}

.upload__btn {
  height: 2.5rem;
  width: 45%;
  border-radius: 0.5rem;
  font-weight: 600;
  margin: 0;
  border: none;
}

.upload__btn--primary {
  background-color: var(--main1-color);
  color: var(--font-color);
}

.upload__btn--secondary {
  background-color: var(--extra-bg);
  color: var(--font-color);
}

.upload__btn--secondary:hover {
  background-color: var(--extra-bg) !important;
}

.meet__container {
  position: relative;
  display: flex;
  /* flex-direction: column; */
  align-items: center;
  min-height: 100%;
  width: 100%;
}

.user__camera {
  width: 100%;
  height: 70vh;
  border-radius: 2rem;
}

.user__avatar {
  object-fit: contain;
  width: 100%;
  height: 100%;
}

.meet__action {
  display: flex;
  gap: 2rem;
  position: fixed;
  bottom: 1rem;
  left: 40%;
  width: fit-content;
}

.meet__action i {
  /* position: absolute; */
  font-size: 2rem;
  cursor: pointer;
  color: var(--main1-color);
}
</style>
