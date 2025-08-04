<template>
  <div class="d-flex" style="min-height: 100%; margin-bottom: 1rem" v-if="owner && notifications">
    <div class="nav__bar" style=" width: 18%">
      <nav-bar></nav-bar>
    </div>
    <div class="" style="width: 82%">
      <header-component :user="owner" :notifications="notifications"></header-component>
      <router-view style="margin-top: 5rem"></router-view>
    </div>
  </div>
</template>

<script setup>
import NavBar from '@/pages/admin/NavBar.vue'
import HeaderComponent from '@/pages/admin/HeaderComponent.vue'
import { onMounted, ref } from 'vue'
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

    // Lắng nghe khi có người gọi
    window.Echo.private(`user.${owner.value.id}`)
      .listen('.call.request', (e) => {
        console.log('BROADCAST NEW CALL ', e)
        const url = `/call-window?channel=${e.channel}&thumb=${e.thumb}&message=${e.message.id}&role=receiver`
        window.open(url, '_blank', 'width=800,height=600')
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
.nav__bar {
  width: 20rem;
}
</style>
