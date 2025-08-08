<template>
  <div v-if="user">
    <router-link :to="{ name: 'auth.forgot' }">
      <i class="fa-solid fa-arrow-left back"></i>
    </router-link>

    <p class="text-center fs-3 fw-semibold fw-bolder">Quên mật khẩu?</p>

    <p class="text-center">
      Xin chào
      <strong>{{ user.profile.name }}</strong>
    </p>
    <p class="text-center">Vui lòng chọn phương thức để lấy lại mật khẩu</p>

    <!-- Loading  -->
    <div class="d-flex justify-content-center flex-column align-items-center" v-if="loading" style="margin: auto">
      <div class="spinner-border"></div>
      <p class="mt-2">Đang gửi gmail</p>
    </div>

    <div v-else>
      <!-- Username -->
      <div>
        <div class="form-group mg-form">
          <!-- Email -->
          <div class="form-floating mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#Model1">
            <p type="text" class="form-control" id="email" name="email">
              {{ user.email }}
              <i v-if="user.email_active == 0">(Chưa kích hoạt)</i>
            </p>
            <label class="label-input" for="email">Email</label>
          </div>

          <!-- Modal -->
          <div
            class="modal fade"
            id="Model1"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
            ref="sendMailModal"
          >
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Email</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div>
                  <div class="modal-body">
                    <p>
                      {{ user.email }}
                      <i v-if="user.email_active == 0">(Chưa kích hoạt)</i>
                    </p>
                    <p class="fw-light">
                      <i>Với phương thức này, sẽ có đường link cấp lại mật khẩu gửi về email của bạn</i>
                    </p>
                  </div>
                  <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" :disabled="user.email_active == 0" @click="sendMail">
                      Gửi
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group mg-form" data-bs-toggle="modal" data-bs-target="#Model2">
          <div class="form-floating mt-3 mb-3">
            <p type="text" class="form-control" id="phone" name="phone">
              {{ user.profile.phone }}
            </p>
            <label class="label-input" for="phone">Số điện thoại</label>

            <!-- Modal -->
            <div class="modal fade" id="Model2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Số điện thoại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div>
                    <div class="modal-body">
                      <p>{{ user.profile.phone }}</p>
                      <p class="fw-light"><i>Hiện tại phương thức này chưa được hỗ trợ</i></p>
                    </div>
                    <div class="modal-footer justify-content-end">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                      <!-- <button type="submit" class="btn btn-primary">Gửi</button> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <hr />
    <p class="text-center">
      Chưa có tài khoản?
      <router-link :to="{ name: 'auth.register' }">
        <a class="text-decoration-none">Đăng ký</a>
      </router-link>
    </p>
  </div>
</template>

<script setup>
// import router from '@/router'
import router from '@/router'
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
const user = ref()
const sendMailModal = ref()
const loading = ref(false)

onMounted(async () => {
  if (sessionStorage.getItem('userForgot') == null) {
    router.push({ name: 'auth.forgot' })
    return
  }

  try {
    let res = await axios.post(`/auth/getResetUser`, {
      user_id: sessionStorage.getItem('userForgot'),
    })
    user.value = res.data.user
  } catch (error) {
    console.log('Không lấy được thông tin User!', error)
    router.push({ name: 'auth.forgot' })
    user.value = null
  }
})

async function sendMail() {
  loading.value = true
  window.bootstrap.Modal.getInstance(sendMailModal.value).hide()
  try {
    const res = await axios.post(`/auth/getTokenForgot`, {
      user_id: sessionStorage.getItem('userForgot'),
    })
    loading.value = false
    toast.success(res.data.message, {
      position: 'top-center',
    })
  } catch (error) {
    toast.error(error.response.data.message, {
      position: 'top-center',
    })
    loading.value = false
  }
}
</script>
