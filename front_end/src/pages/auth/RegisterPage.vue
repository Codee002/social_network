<template>
  <p class="text-center fs-3 fw-semibold fw-bolder">Đăng Ký</p>
  <!-- Name -->
  <form @submit.prevent="onSubmit()" method="POST">
    <div class="form-group mg-form">
      <div class="form-floating mb-3 mt-3">
        <input
          type="text"
          class="form-control"
          id="name"
          placeholder="Enter name"
          name="name"
          autocomplete="off"
          v-model="form.name"
          :class="{ 'is-invalid': errors.name }"
        />
        <label class="label-input" for="name">Họ tên</label>
        <span v-if="errors.name" class="invalid-feedback" style="display: block">
          <strong>{{ errors.name }}</strong>
        </span>
      </div>
    </div>

    <!-- Username -->
    <div class="form-group mg-form">
      <div class="form-floating mb-3 mt-3">
        <input
          type="text"
          class="form-control"
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

    <div class="form-group mg-form">
      <label for="accept">
        <input
          required=""
          type="checkbox"
          value="1"
          id="accept"
          name="accept"
          class="form-check-input"
          v-model="form.check"
        />
        Bằng việc Đăng ký, bạn đã đọc và đồng ý với
        <a href="" class="text-decoration-none">Điều khoản sử dụng</a>
        và
        <a href="" class="text-decoration-none">Chính sách bảo mật</a>
        của chúng tôi
      </label>
      <span v-if="errors.check" class="invalid-feedback" style="display: block">
        <strong>{{ errors.check }}</strong>
      </span>
    </div>

    <button type="submit" class="btn btn-primary mg-btn">Đăng ký</button>
  </form>

  <hr />

  <p class="text-center">
    Đã có tài khoản?
    <router-link :to="{ name: 'auth.login' }"><a class="text-decoration-none">Đăng nhập</a></router-link>
  </p>
</template>

<script setup>
// ----------------------- Import -----------------------
import { reactive, defineEmits } from 'vue'
import validate from '@/utils/validate'
import axios from 'axios'
import { useToast } from 'vue-toastification'
import router from '@/router'

// ----------------------- Variable -----------------------
const toast = useToast()
const errors = reactive([])
const form = reactive({
  name: '',
  username: '',
  password: '',
  password_confirmation: '',
  capcha: '',
  check: false,
})
const message = reactive({})
const emit = defineEmits(['changeTypePassword'])
sessionStorage.removeItem('userChangePassword')
sessionStorage.removeItem('userForgot')

// ----------------------- Function -----------------------
async function onSubmit() {
  // Validate
  errors.username = validate.username(form.username)
  errors.name = validate.name(form.name)
  errors.password = validate.password(form.password)
  errors.password_confirmation = validate.passwordConfirm(form.password, form.password_confirmation)
  if (form.check == false) errors.check = 'Vui lòng đồng ý với điều khoản sử dụng của chúng tôi'
  else errors.check = ''

  //  Nếu không có lỗi
  const hasErrors = Object.values(errors).some((error) => error && error != '')

  if (hasErrors != true) {
    try {
      const res = await axios.post(`/auth/register`, form)
      toast.success(res.data.message, {
        position: 'top-center',
      })
      message.success = res.data.message
      router.push({ name: 'auth.login' })
      // console.log("try")
    } catch (error) {
      console.log("khong thanh cong")
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
