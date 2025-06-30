<template>
  <div v-if="owner && notifications" style="height: 100%">
    <header-component :user="owner" :notifications="notifications"></header-component>
    <div style="padding-top: 3.5rem"></div>
    <router-view :owner="owner"></router-view>
  </div>
  <div class="spinner-border" v-else style="margin: auto"></div>
</template>

<script setup>
// import axios from 'axios'
import { onMounted, ref } from 'vue'
import HeaderComponent from './partials/HeaderComponent.vue'
import auth from '@/utils/auth'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const owner = ref(null)
const notifications = ref([])

// Lấy thông tin chủ tài khoản
onMounted(async () => {
  try {
    owner.value = await auth.getOwner()
    let res = await axios.get(`getNotifications/${owner.value.id}`)
    notifications.value = res.data.notifications

    // Lắng nghe thông báo
    window.Echo.private(`user.${owner.value.id}`)
      .listen('.notification.request', (e) => {
        console.log('NEW NOTIFICATION ', e.notification)
        notifications.value.unshift(e.notification)
        toast.info(`${e.notification.userName} ${e.notification.content}`, {
          position: 'bottom-right',
        })
      })
      .error((error) => {
        console.error('Echo error:', error)
      })
  } catch (error) {
    console.log('Không lấy được thông tin User!', error)
    owner.value = null
  }
})
</script>


<style scoped>
.spinner-border {
  position: fixed;
  top: 50%;
  left: 50%;
}
</style>
