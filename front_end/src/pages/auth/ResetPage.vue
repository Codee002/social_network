<template>
  <div v-if="user">
    <p class="text-center fs-3 fw-semibold fw-bolder">Đổi mật khẩu</p>
    <!-- Name -->
    <form @submit.prevent="onSubmit()" method="POST">
      <!-- Username -->
      <div class="form-group mg-form">
        <div class="form-floating mb-3 mt-3">
          <input
            type="text"
            class="form-control"
            :value="user.username"
            autocomplete="off"
            :class="{ 'is-invalid': errors.username }"
            disabled
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

      <!-- PassConfirm -->
      <div class="form-group mg-form">
        <div class="form-floating mt-3 mb-3">
          <input
            type="password"
            class="form-control password-mg"
            id="password_confirm"
            placeholder="Enter password_confirm"
            name="password_confirm"
            style="background-image: none"
            v-model="form.password_confirmation"
            :class="{ 'is-invalid': errors.password_confirmation }"
          />
          <i class="fa-solid fa-eye pwd-eye" @click="onHidePassword($event)"></i>
          <label class="label-input" for="password_confirm">Nhập lại mật khẩu</label>
          <span v-if="errors.password_confirmation" class="invalid-feedback" style="display: block">
            <strong>{{ errors.password_confirmation }}</strong>
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
      <input type="text" class="form-control" placeholder="" name="capcha" />
      <label class="label-input" for="">Capcha</label>

      <img
        id="capcha-img"
        src=""
        style="height: 100%"
        class="ms-3 me-3"
      />

      <i id="reload-capcha" class="fa-solid fa-rotate-right"></i>
    </div>
  </div> -->

      <button type="submit" class="btn btn-primary mg-btn">Đổi mật khẩu</button>
    </form>

    <hr />

    <p class="text-center">
      Đã có tài khoản?
      <router-link :to="{ name: 'auth.login' }"><a class="text-decoration-none">Đăng nhập</a></router-link>
    </p>
  </div>
</template>

<script setup>
// ----------------------- Import -----------------------
import { reactive, defineEmits, onMounted, ref } from 'vue'
import validate from '@/utils/validate'
import axios from 'axios'
import { useToast } from 'vue-toastification'
import router from '@/router'

// ----------------------- Variable -----------------------
const toast = useToast()
const errors = reactive([])
const form = reactive({
  password: '',
  password_confirmation: '',
})
const message = reactive({})
const emit = defineEmits(['changeTypePassword'])
const user = ref()
// ----------------------- Function -----------------------
onMounted(async () => {
  if (sessionStorage.getItem('userChangePassword') == null) {
    router.push({ name: 'auth.forgot' })
    return
  }

  try {
    let res = await axios.post(`/auth/getAccount/`, {
      user_id: sessionStorage.getItem('userChangePassword'),
    })
    user.value = res.data.user
  } catch (error) {
    console.log('Có lỗi xảy ra với token!', error)
    router.push({ name: 'auth.tokenError' })
  }
})

async function onSubmit() {
  // Validate
  errors.password = validate.password(form.password)
  errors.password_confirmation = validate.passwordConfirm(form.password, form.password_confirmation)

  //  Nếu không có lỗi
  const hasErrors = Object.values(errors).some((error) => error && error != '')

  if (hasErrors != true) {
    try {
      form.user_id = user.value.id
      const res = await axios.post(`/auth/handleReset`, form)
      toast.success(res.data.message, {
        position: 'top-center',
      })
      message.success = res.data.message
      router.push({ name: 'auth.login' })

    } catch (error) {
      if (error.response) {
        const resData = error.response.data
        const errorMessages = resData.errors
        for (let key in errorMessages) {
          errors[key] = errorMessages[key][0]
        }
      }
    }
  }
}

function onHidePassword(event) {
  emit('changeTypePassword', event)
}

// ----------------------- Hook -----------------------
</script>
