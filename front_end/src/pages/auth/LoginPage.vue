<template>
  <form @submit.prevent="onSubmit()" method="POST">
    <div class="form-group mg-form">
      <!-- Username -->
      <div class="form-group mg-form">
        <p class="text-center fs-3 fw-semibold fw-bolder">Đăng nhập</p>
        <!-- <img :src="srcImg" alt=""> -->
        <div class="form-floating mb-3 mt-3">
          <input
            type="text"
            class="form-control"
            value=""
            id="username"
            placeholder="Enter username"
            name="username"
            autocomplete="off"
            v-model="form.username"
            :class="{ 'is-invalid': errors.username }"
          />
          <label class="label-input" for="username">Tên đăng nhập</label>
          <span v-if="errors.username" class="invalid-feedback" style="display: block">
            <strong>{{ errors.username }}</strong>
          </span>
        </div>
      </div>

      <!-- Password -->
      <div class="form-group mg-form">
        <div class="form-floating mt-3 mb-3">
          <input
            type="password"
            class="form-control pwd-mg"
            id="password"
            placeholder="Enter password"
            name="password"
            style="background-image: none"
            v-model="form.password"
            :class="{ 'is-invalid': errors.password }"
          />
          <i class="fa-solid fa-eye pwd-eye" @click="onHidePassword($event)"></i>
          <label class="label-input" for="password">Mật khẩu</label>
          <span v-if="errors.password" class="invalid-feedback" style="display: block">
            <strong>{{ errors.password }}</strong>
          </span>
        </div>
      </div>

      <!-- Capcha -->
      <!-- <div class="form-group mg-form mb-3" style="align-items: center">
      <div
        class="form-floating mt-3 mb-0 d-flex justify-content-start"
        style="align-items: center"
        id="capcha-group"
      >
        <input type="text" class="form-control" placeholder="" name="captcha" />
        <label class="label-input" for="">Capcha</label>

        <img
          id="capcha-img"
          src="data:image/jpeg;base64,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"
          style="height: 100%"
          class="ms-3 me-3"
        />

        <i
          id="reload-capcha"
          onclick="reloadCapcha()"
          class="fa-solid fa-rotate-right"
        ></i>
      </div>
    </div> -->

      <button type="submit" class="btn btn-primary mg-btn">Đăng nhập</button>

      <hr />
      <p class="text-center">
        Chưa có tài khoản?
        <router-link :to="{ name: 'auth.register' }">
          <a class="text-decoration-none">Đăng ký</a>
        </router-link>
      </p>
      <p class="text-center">
        <router-link :to="{ name: 'auth.forgot' }">
          <a class="text-decoration-none">Quên mật khẩu?</a>
        </router-link>
      </p>

      <p class="text-center footer-form">
        <a href="">Hỗ trợ</a>
        |
        <a href="">Chính sách &amp; bảo mật</a>
      </p>
    </div>
  </form>
</template>



<script setup>
// ----------------------- Import -----------------------
import router from '@/router'
import axios from 'axios'
import { reactive, defineEmits } from 'vue'
import { useToast } from 'vue-toastification'
import ToastAlert from '../../components/ToastAlert.vue'
import { h } from 'vue'

// ----------------------- Variable -----------------------
const errors = reactive({})
const toast = useToast()
const form = reactive({
  username: '',
  password: '',
  capcha: '',
})
const emit = defineEmits(['changeTypePassword'])
// const srcImg = ref(require("@/assets/images/general/login.png"))

// ----------------------- Function -----------------------
async function onSubmit() {
  // Validate
  if (!form.username || form.username.trim().length == 0) errors.username = 'Tên đăng nhập không được để trống'
  else errors.username = ''

  if (!form.password || form.password.trim().length == 0) errors.password = 'Mật khẩu không được để trống'
  else errors.password = ''

  const hasErrors = Object.values(errors).some((error) => error && error != '')
  if (hasErrors != true) {
    try {
      const res = await axios.post(`/auth/login`, form)
      localStorage.setItem('auth_token', res.data.auth_token)
      localStorage.setItem('owner', JSON.stringify(res.data.user))
      axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('auth_token')}`
      if (res.data.user.role == 'user') router.push({ name: 'home' })
      else if (res.data.user.role == 'admin') router.push({name: "admin.account"})
    } catch (error) {
      if (error.response) {
        const resData = error.response.data
        toast.error(
          h(ToastAlert, {
            message: resData.errors.message,
          }),
          {
            position: 'top-center',
            timeout: 5000,
            closeOnClick: true,
          }
        )
      }
    }
  }
}

function onHidePassword(event) {
  emit('changeTypePassword', event)
}
</script>
