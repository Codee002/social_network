<template>
  <div class="d-flex justify-content-center flex-column align-items-center" v-if="loading" style="margin: auto">
    <div class="spinner-border"></div>
    <p>Đang kiểm tra</p>
  </div>
</template>

<script setup>
// import router from '@/router'
import router from '@/router'
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'

const loading = ref(true)
const route = useRoute()
const userId = route.params.user_id
const type = route.params.type
const token = route.params.token
const toast = useToast()

onMounted(async () => {
  // if (sessionStorage.getItem('userForgot') == null || userId != sessionStorage.getItem('userForgot')) {
  //   router.push({ name: 'auth.tokenError' })
  //   return
  // }

  try {
    if (type != 'twoStepAuth') {
      await axios.post(`/auth/checkToken`, {
        user_id: userId,
        token: token,
      })
      // Đổi mật khẩu
      if (type == 'changePassword') {
        sessionStorage.setItem('userChangePassword', userId)
        router.push({ name: 'auth.reset' })
      }

      // Xác thực gmail
      else if (type == 'emailActive') {
        let res = await axios.post(`/auth/activeEmail`, {
          user_id: userId,
        })
        console.log('Xác thực email')
        toast.success(res.data.message, {
          position: 'bottom-right',
        })
        router.push({ name: 'setting.info' })
      }
    } else {
      // Xác thực 2 bước
      let res = await axios.post(`/auth/checkLoginToken`, {
        user_id: userId,
        token: token,
      })
      console.log(userId, token)
      toast.success(res.data.message, {
        position: 'bottom-right',
      })

      // Lưu thông tin đăng nhập
      localStorage.setItem('auth_token', res.data.auth_token)
      localStorage.setItem('owner', JSON.stringify(res.data.user))
      axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('auth_token')}`
      if (res.data.user.role == 'user') router.push({ name: 'home' })
      else if (res.data.user.role == 'admin') router.push({ name: 'admin.account' })
    }
  } catch (error) {
    console.log('Có lỗi xảy ra với token!', error)
    router.push({ name: 'auth.tokenError' })
  }
})
</script>
