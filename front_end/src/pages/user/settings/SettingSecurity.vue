<template>
  <div class="settingUserInfo col-10 col-sm-8 offset-sm-1 offset-lg-1 col-lg-7 offset-xl-1 col-xl-7">
    <p class="settingUserInfo__title">Bảo mật</p>

    <!-- FLASH MESSAGE -->

    <div class="settingUserInfo__navWrapper settingSecurity__navWrapper row">
      <div
        class="settingUserInfo__navWrapper__nav settingSecurity_navWrapper__nav col-12 col-lg-10 offset-lg-1"
        data-bs-toggle="modal"
        data-bs-target="#Model1"
      >
        <i class="fa-solid fa-angle-right settingUserInfo__navWrapper__next"></i>
        <p class="settingUserInfo__navWrapper__nav__title settingSecurity_navWrapper__nav__title">Đổi mật khẩu</p>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="Model1" aria-labelledby="exampleModalLabel" aria-hidden="true" ref="passwordModal">
        <div class="modal-dialog">
          <div class="modal-content settingUserInfo__navWrapper__modalBackground">
            <div class="modal-header">
              <strong>
                <h1 class="modal-title text-center fs-5 ms-auto">Đổi mật khẩu</h1>
              </strong>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="getPassword" @submit.prevent="changePassword">
              <div class="modal-body">
                <div class="row">
                  <label for="oldpass" class="form-label">Mật khẩu cũ:</label>
                  <div class="form-group">
                    <input
                      type="password"
                      name="oldpass"
                      id="oldpass"
                      placeholder="Nhập mật khẩu"
                      class="form-control mb-3 settingUserInfo__navWrapper__modalBackground"
                      v-model="form.oldpass"
                      :class="{ 'is-invalid': errors.oldpass }"
                    />
                    <i class="fa-solid fa-eye pwd-eye" @click="changeTypePassword"></i>

                    <span v-if="errors.oldpass" class="mb-3 invalid-feedback" style="display: block">
                      <strong>{{ errors.oldpass }}</strong>
                    </span>
                  </div>

                  <label for="password" class="form-label">Mật khẩu mới:</label>
                  <div class="form-group">
                    <input
                      type="password"
                      name="password"
                      id="password"
                      placeholder="Nhập mật khẩu"
                      class="form-control mb-3 settingUserInfo__navWrapper__modalBackground"
                      v-model="form.password"
                      :class="{ 'is-invalid': errors.password }"
                    />
                    <i class="fa-solid fa-eye pwd-eye" @click="changeTypePassword"></i>

                    <span v-if="errors.password" class="mb-3 invalid-feedback" style="display: block">
                      <strong>{{ errors.password }}</strong>
                    </span>
                  </div>

                  <label for="password_confirm" class="form-label">Nhập lại mật khẩu:</label>
                  <div class="form-group">
                    <input
                      type="password"
                      name="password_confirm"
                      id="password_confirm"
                      placeholder="Nhập lại mật khẩu"
                      class="form-control mb-3 settingUserInfo__navWrapper__modalBackground"
                      v-model="form.password_confirmation"
                      :class="{ 'is-invalid': errors.password_confirmation }"
                    />
                    <i class="fa-solid fa-eye pwd-eye" @click="changeTypePassword"></i>

                    <span v-if="errors.password_confirmation" class="mb-3 invalid-feedback" style="display: block">
                      <strong>{{ errors.password_confirmation }}</strong>
                    </span>
                  </div>
                </div>
              </div>
              <div class="modal-footer d-flex justify-content-between settingUserInfo__navWrapper__modalBackground">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div
        class="settingUserInfo__navWrapper__nav settingSecurity_navWrapper__nav settingUserInfo__navWrapper__nav--last col-12 col-lg-10 offset-lg-1"
        data-bs-toggle="modal"
        data-bs-target="#Modal2"
      >
        <i class="fa-solid fa-angle-right settingUserInfo__navWrapper__next"></i>
        <p class="settingUserInfo__navWrapper__nav__title settingSecurity_navWrapper__nav__title">
          Xác thực 2 bước
          <i v-if="userProfile.two_step_auth == 0">(Chưa kích hoạt)</i>
          <i v-if="userProfile.two_step_auth == 1">(Đang kích hoạt)</i>
        </p>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="Modal2" aria-labelledby="exampleModalLabel" aria-hidden="true" ref="twoStepAuthModal">
        <div class="modal-dialog">
          <div class="modal-content settingUserInfo__navWrapper__modalBackground">
            <div class="modal-header" style="position: relative">
              <h1 class="modal-title text-center fs-5 ms-auto">Xác thực 2 bước</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body mt-2 mb-2">
              <p class="fs-6">Khi bật xác thực 2 bước. Bạn sẽ phải xác thực gmail trước khi đăng nhập.</p>
            </div>
            <div class="modal-footer d-flex justify-content-between settingUserInfo__navWrapper__modalBackground">
              <button type="button" data-bs-dismiss="modal" class="btn btn-danger">Hủy bỏ</button>
              <div>
                <a href="#" @click="twoStepAuth(0)"><button type="button" class="btn btn-secondary">Tắt</button></a>
                <a href="#" @click="twoStepAuth(1)">
                  <button type="submit" class="btn btn-primary ms-2">Bật</button>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, reactive, ref } from 'vue'
import validate from '@/utils/validate'
import { useToast } from 'vue-toastification'
import axios from 'axios'

const props = defineProps({
  owner: {},
})

const userProfile = reactive({})
Object.assign(userProfile, props.owner)

const form = reactive({
  oldpass: '',
  password: '',
  password_confirmation: '',
})

const passwordModal = ref()
const twoStepAuthModal = ref()
const errors = reactive({})
const toast = useToast()

async function changePassword() {
  errors.oldpass = null
  errors.password = validate.password(form.password)
  errors.password_confirmation = validate.passwordConfirm(form.password, form.password_confirmation)

  //  Nếu không có lỗi
  const hasErrors = Object.values(errors).some((error) => error && error != '')
  console.log(form)
  if (!hasErrors) {
    try {
      const res = await axios.post(`/changePassword`, form)
      toast.success(res.data.message, {
        position: 'bottom-right',
      })
      window.bootstrap.Modal.getInstance(passwordModal.value).hide()
    } catch (error) {
      if (error.response) {
        console.log(error)
        const resData = error.response.data
        const errorMessages = resData.errors
        for (let key in errorMessages) {
          errors[key] = errorMessages[key][0]
        }
      }
    }
  }
}

function changeTypePassword(event) {
  const input = event.target.parentElement.querySelector('input')
  if (input.type == 'password') {
    input.type = 'text'
    event.target.classList.replace('fa-eye', 'fa-eye-slash')
  } else {
    input.type = 'password'
    event.target.classList.replace('fa-eye-slash', 'fa-eye')
  }
}

// Bảo mật 2 lớp
async function twoStepAuth(type) {
  try {
    const res = await axios.post(`/twoStepAuth`, {
      type: type,
    })
    toast.success(res.data.message, {
      position: 'bottom-right',
    })
    userProfile.two_step_auth = res.data.two_step_auth
    window.bootstrap.Modal.getInstance(twoStepAuthModal.value).hide()
  } catch (error) {
    console.log('Có lỗi khi đổi trạng thái xác thực', error)
    toast.error(error.response.data.message, {
      position: 'bottom-right',
    })
  }
}
</script>

<style scoped>
.settingUserInfo {
  margin-top: 2rem;
  margin-bottom: 5rem;
  background-color: var(--main-extra-bg);
  border-radius: 1rem;
  padding: 2rem 3rem;
  /* min-height: 100vh; */
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.1rem;
}

.settingUserInfo__title {
  font-size: 1.4rem;
  font-weight: 600;
  margin-bottom: 2rem;
}

.settingSecurity_navWrapper__nav {
  display: flex;
  justify-content: center;
  height: 3rem;
  position: relative;
  flex-direction: column;
  padding: 0.4rem;
  border: 1px solid var(--extra-bg);
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.05rem;
}

.settingUserInfo__navWrapper__nav:hover {
  background-color: var(--hover-color);
  cursor: pointer;
}

.settingSecurity_navWrapper__nav:first-child {
  border-top-left-radius: 1rem;
  border-top-right-radius: 1rem;
}

.settingUserInfo__navWrapper__nav--last {
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
}

.settingUserInfo__navWrapper__next {
  position: absolute;
  right: 2rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.2rem;
}

.settingSecurity_navWrapper__nav__title {
  margin-left: 1rem;
  font-size: 1rem;
  font-weight: 550;
  margin-bottom: 0.1rem;
}

/* Modal */
.modal-content {
  margin-top: 5rem;
}

.modal-dialog .form-group {
  position: relative;
}

.modal-dialog .form-control {
  background-color: var(--main-extra-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 0.6rem !important;
  color: var(--font-color) !important;
}

.pwd-eye {
  position: absolute;
  top: 0.7rem;
  right: 1.5rem;
  color: var(--font-input-color);
}
</style>
