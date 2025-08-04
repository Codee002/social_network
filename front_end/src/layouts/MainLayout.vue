<template>
  <div v-if="owner && notifications && listFriend" style="height: 100%">
    <header-component :user="owner" :notifications="notifications"></header-component>
    <div style="padding-top: 3.5rem"></div>
    <router-view
      :owner="owner"
      :listFriend="listFriend"
      :key="$route.name === 'profile' ? $route.fullPath : undefined"
    ></router-view>
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
const notifications = ref()
const listFriend = ref()

// Lấy thông tin chủ tài khoản
onMounted(async () => {
  try {
    owner.value = await auth.getOwner()
    let res = await axios.get(`getNotifications/${owner.value.id}`)
    notifications.value = res.data.notifications

    // Lấy DS bạn bè
    res = await axios.get(`/getFriends/${owner.value.id}`)
    listFriend.value = res.data.friendList

    // Lắng nghe thông báo
    window.Echo.private(`user.${owner.value.id}`)
      .listen('.notification.request', (e) => {
        // Nếu tài khoản không bị khóa
        if (JSON.parse(localStorage.getItem('owner')).status == 'actived') {
          console.log('NEW NOTIFICATION ', e.notification)
          notifications.value.unshift(e.notification)
          toast.info(`${e.notification.userName} ${e.notification.content}`, {
            position: 'bottom-right',
          })
        }
      })
      .error((error) => {
        console.error('Echo error:', error)
      })

    // Lắng nghe khi có người gọi
    window.Echo.private(`user.${owner.value.id}`)
      .listen('.call.request', (e) => {
        // Nếu tài khoản không bị khóa
        if (JSON.parse(localStorage.getItem('owner')).status == 'actived') {
          console.log('BROADCAST NEW CALL ', e)
          const url = `/call-window?channel=${e.channel}&thumb=${e.thumb}&message=${e.message.id}&role=receiver`
          window.open(url, '_blank', 'width=800,height=600')
        }
      })

      .error((error) => {
        console.error('Echo error:', error)
      })

    // Lắng nghe khi trạng thái tài khoản thay đổi
    window.Echo.private(`user.${owner.value.id}`)
      .listen('.account.changeStatus', (e) => {
        console.log('BROADCAST CHANGE STATUS ', e)
        localStorage.setItem('owner', JSON.stringify(e.user))
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
