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
    <div v-if="state === 'accepted'" class="meet__container">
      <div>
        <p>Bạn</p>
        <div ref="localRef" style="width: 30rem; height: 30rem"></div>
      </div>
      <div>
        <p>Người bên kia</p>
        <div ref="remoteRef" style="width: 30rem; height: 30rem"></div>
      </div>

      <div class="meet__action">
        <i class="fa-solid fa-microphone"></i>
        <i class="fa-solid fa-microphone-slash"></i>
        <i class="fa-solid fa-video"></i>
        <i class="fa-solid fa-video-slash"></i>
        <i class="fa-solid fa-phone-slash"></i>
      </div>
    </div>
  </div>
</template>


<script setup>
import { useRoute } from 'vue-router'
import { computed, getCurrentInstance, onBeforeUnmount, onMounted, ref } from 'vue'
const { proxy } = getCurrentInstance()
import AgoraRTC from 'agora-rtc-sdk-ng'
import axios from 'axios'

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

// Chấp nhận cuộc gọi
async function acceptCall() {
  try {
    const res = await axios.post('/conversation/acceptCall', { channel, message_id: message })
    console.log('Chấp nhận thành công', res)
    window.location.href = `/call-window?channel=${encodeURIComponent(channel)}&token=${encodeURIComponent(
      res.data.token
    )}&uid=${res.data.uid}&role=receiver&accepted=1`
  } catch (error) {
    console.log(error)
  }
}

console.log('CHANNEL', channel)
console.log('token', token)
console.log('uid', uid)

// Khi đã chấp nhận
// Thông tin agora
const localRef = ref(null)
const remoteRef = ref(null)
const client = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' })
let localTrack, audioTrack

async function joinAgora() {
  try {
    await navigator.mediaDevices.getUserMedia({ video: true, audio: true })
    console.log('Media permissions granted')
  } catch (error) {
    console.error('Media permissions error:', error)
    alert('Vui lòng cấp quyền truy cập camera và microphone!')
  }

  try {
    console.log('CHANNEL', channel)
    console.log('token', token)
    console.log('uid', uid)
    await client.join(process.env.VUE_APP_AGORA_APP_ID, channel, token, uid)
    localTrack = await AgoraRTC.createCameraVideoTrack()
    audioTrack = await AgoraRTC.createMicrophoneAudioTrack()
    localTrack.play(localRef.value)
    await client.publish([localTrack, audioTrack])

    client.on('user-published', async (user, mediaType) => {
      await client.subscribe(user, mediaType)
      if (mediaType === 'video') user.videoTrack.play(remoteRef.value)
      if (mediaType === 'audio') user.audioTrack.play()
    })
  } catch (error) {
    console.error('Agora join error:', error)
  }
}

onMounted(async () => {
  if (role === 'receiver') {
    if (route.query.accepted === '1') {
      state.value = 'accepted'
      await joinAgora()
    }
  }
})

onBeforeUnmount(async () => {
  await client.leave()
  localTrack?.stop()
  localTrack?.close()
  audioTrack?.close()
  // leaveCall()
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
  height: 100%;
  width: 100%;
}

.meet__action {
  display: flex;
  gap: 2rem;
  position: absolute;
  bottom: 1rem;
  left: 40%;
}

.meet__action i {
  /* position: absolute; */
  font-size: 2rem;
  cursor: pointer;
  color: var(--main1-color);
}
</style>
