<template>
  <div class="settingUserInfo col-10 col-sm-8 offset-sm-1 offset-lg-1 col-lg-7 offset-xl-1 col-xl-7" v-if="userProfile">
    <p class="settingUserInfo__title">Thông tin cá nhân</p>
    <div class="settingUserInfo__navWrapper row">
      <div
        class="settingUserInfo__navWrapper__nav col-12 col-lg-10 offset-lg-1"
        data-bs-toggle="modal"
        data-bs-target="#Model1"
      >
        <i class="fa-solid fa-angle-right settingUserInfo__navWrapper__next"></i>
        <p class="settingUserInfo__navWrapper__nav__title">Thông tin liên lạc</p>
        <p class="settingUserInfo__navWrapper__nav__info mb-0">
          {{ userProfile.email ?? 'Chưa có email' }}
          <i class="" v-if="userProfile.email && userProfile.email_active == 0">(Chưa kích hoạt)</i>
        </p>
        <p class="settingUserInfo__navWrapper__nav__info">{{ userProfile.profile.phone ?? 'Chưa có số điện thoại' }}</p>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="Model1" aria-labelledby="exampleModalLabel" aria-hidden="true" ref="infoModal">
        <div class="modal-dialog">
          <div class="modal-content settingUserInfo__navWrapper__modalBackground">
            <div class="modal-header">
              <strong>
                <h1 class="modal-title text-center fs-5 ms-auto">Thông tin liên lạc</h1>
              </strong>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form @submit.prevent="changeEmailAndPhone">
              <div class="modal-body">
                <div class="row">
                  <label for="email" class="form-label">Email:</label>
                  <div class="">
                    <input
                      type="text"
                      name="email"
                      id="email"
                      class="form-control mb-3 settingUserInfo__navWrapper__modalBackground"
                      v-model="form.email"
                      placeholder="Nhập vào email"
                      :class="{ 'is-invalid': errors.email }"
                    />
                    <span
                      v-if="userProfile.email && userProfile.email_active == 0"
                      id="check_active_email"
                      class="text-danger d-block"
                      style="margin-top: -1rem; margin-bottom: 0.5rem"
                    >
                      Kích hoạt email
                      <router-link @click="activeEmail" :to="{ name: 'setting.emailActive' }" class="text-danger">
                        <strong>tại đây</strong>
                      </router-link>
                    </span>
                    <span v-if="errors.email" class="mb-3 invalid-feedback" style="display: block">
                      <strong>{{ errors.email }}</strong>
                    </span>
                  </div>

                  <label for="phone_number" class="form-label">Số điện thoại:</label>
                  <div class="">
                    <input
                      type="text"
                      name="phone_number"
                      id="phone_number"
                      class="form-control mb-3 settingUserInfo__navWrapper__modalBackground"
                      placeholder="Nhập vào số điện thoại"
                      v-model="form.phone"
                      :class="{ 'is-invalid': errors.phone }"
                    />
                    <span v-if="errors.phone" class="mb-3 invalid-feedback" style="display: block">
                      <strong>{{ errors.phone }}</strong>
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
        class="settingUserInfo__navWrapper__nav col-12 col-lg-10 offset-lg-1"
        data-bs-toggle="modal"
        data-bs-target="#Model2"
      >
        <i class="fa-solid fa-angle-right settingUserInfo__navWrapper__next"></i>
        <p class="settingUserInfo__navWrapper__nav__title">Tên</p>
        <p class="settingUserInfo__navWrapper__nav__info">{{ userProfile.profile.name ?? 'Chưa có tên' }}</p>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="Model2" aria-labelledby="exampleModalLabel" aria-hidden="true" ref="nameModal">
        <div class="modal-dialog">
          <div class="modal-content settingUserInfo__navWrapper__modalBackground">
            <div class="modal-header">
              <strong>
                <h1 class="modal-title text-center fs-5 ms-auto">Tên</h1>
              </strong>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form @submit.prevent="changeName">
              <div class="modal-body">
                <div class="row">
                  <label for="name" class="form-label">Tên:</label>
                  <div class="">
                    <input
                      type="text"
                      name="name"
                      id="name"
                      class="form-control mb-3 settingUserInfo__navWrapper__modalBackground"
                      v-model="form.name"
                      placeholder="Nhập vào họ tên"
                      :class="{ 'is-invalid': errors.name }"
                    />
                    <span v-if="errors.name" class="mb-3 invalid-feedback" style="display: block">
                      <strong>{{ errors.name }}</strong>
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
        class="settingUserInfo__navWrapper__nav col-12 col-lg-10 offset-lg-1"
        data-bs-toggle="modal"
        data-bs-target="#Model3"
      >
        <i class="fa-solid fa-angle-right settingUserInfo__navWrapper__next"></i>
        <p class="settingUserInfo__navWrapper__nav__title">Ngày sinh</p>
        <p class="settingUserInfo__navWrapper__nav__info">
          {{
            userProfile.profile.birthday
              ? $dayjs(userProfile.profile.birthday).format('DD-MM-YYYY')
              : 'Chưa có năm sinh'
          }}
        </p>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="Model3" aria-labelledby="exampleModalLabel" aria-hidden="true" ref="birthdayModal">
        <div class="modal-dialog">
          <div class="modal-content settingUserInfo__navWrapper__modalBackground">
            <div class="modal-header">
              <strong>
                <h1 class="modal-title text-center fs-5 ms-auto">Ngày sinh</h1>
              </strong>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form @submit.prevent="changeBirthday">
              <div class="modal-body">
                <div class="row">
                  <label for="birth" class="form-label">Ngày:</label>
                  <div class="">
                    <input
                      type="date"
                      name="birth"
                      id="birth"
                      class="form-control mb-3 settingUserInfo__navWrapper__modalBackground"
                      v-model="form.birthday"
                      placeholder="Nhập vào năm sinh"
                      :class="{ 'is-invalid': errors.birthday }"
                    />
                    <span v-if="errors.birthday" class="mb-3 invalid-feedback" style="display: block">
                      <strong>{{ errors.birthday }}</strong>
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
        class="settingUserInfo__navWrapper__nav col-12 col-lg-10 offset-lg-1"
        data-bs-toggle="modal"
        data-bs-target="#Model4"
      >
        <i class="fa-solid fa-angle-right settingUserInfo__navWrapper__next"></i>
        <p class="settingUserInfo__navWrapper__nav__title">Giới tính</p>
        <p class="settingUserInfo__navWrapper__nav__info">{{ userGender }}</p>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="Model4" aria-labelledby="exampleModalLabel" aria-hidden="true" ref="genderModal">
        <div class="modal-dialog">
          <div class="modal-content settingUserInfo__navWrapper__modalBackground">
            <div class="modal-header">
              <strong>
                <h1 class="modal-title text-center fs-5 ms-auto">Giới tính</h1>
              </strong>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form @submit.prevent="changeGender">
              <div class="modal-body">
                <div class="row">
                  <label for="gender" class="form-label">Giới tính:</label>
                  <div class="">
                    <input
                      type="radio"
                      v-model="form.gender"
                      class="form-check-input mb-3 settingUserInfo__navWrapper__modalBackground"
                      name="gender"
                      value="male"
                      :class="{ 'is-invalid': errors.gender }"
                    />
                    Nam
                    <input
                      type="radio"
                      class="ms-1 form-check-input mb-3 settingUserInfo__navWrapper__modalBackground"
                      name="gender"
                      value="female"
                      v-model="form.gender"
                      :class="{ 'is-invalid': errors.gender }"
                    />
                    Nữ
                    <span v-if="errors.gender" class="mb-3 invalid-feedback" style="display: block">
                      <strong>
                        {{ errors.gender }}
                      </strong>
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
        class="settingUserInfo__navWrapper__nav settingUserInfo__navWrapper__nav--last col-12 col-lg-10 offset-lg-1"
        data-bs-toggle="modal"
        data-bs-target="#Model5"
      >
        <i class="fa-solid fa-angle-right settingUserInfo__navWrapper__next"></i>
        <p class="settingUserInfo__navWrapper__nav__title">Quê quán</p>
        <p class="settingUserInfo__navWrapper__nav__info">{{ userProfile.profile.address ?? 'Chưa có quê quán' }}</p>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="Model5" aria-labelledby="exampleModalLabel" aria-hidden="true" ref="addressModal">
        <div class="modal-dialog">
          <div class="modal-content settingUserInfo__navWrapper__modalBackground">
            <div class="modal-header">
              <strong>
                <h1 class="modal-title text-center fs-5 ms-auto">Quê quán</h1>
              </strong>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form @submit.prevent="changeAddress">
              <div class="modal-body">
                <div class="row">
                  <label for="address" class="form-label">Quê quán:</label>
                  <div class="">
                    <input
                      type="text"
                      id="address"
                      name="address"
                      class="form-control mb-3 settingUserInfo__navWrapper__modalBackground"
                      v-model="form.address"
                      placeholder="Nhập vào quê quán"
                      :class="{ 'is-invalid': errors.address }"
                    />
                    <span v-if="errors.address" class="mb-3 invalid-feedback" style="display: block">
                      <strong>{{ errors.address }}</strong>
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
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps, reactive, ref } from 'vue'
import validate from '@/utils/validate'
import { useToast } from 'vue-toastification'
import axios from 'axios'

const infoModal = ref()
const nameModal = ref()
const birthdayModal = ref()
const genderModal = ref()
const addressModal = ref()
const toast = useToast()
const props = defineProps({
  owner: {},
})

const userGender = computed(() => {
  if (!props.owner.profile.gender) return 'Chưa có giới tính'

  if (props.owner.profile.gender == 'male') return 'Nam'
  else return 'Nữ'
})

const form = reactive({
  email: props.owner.email,
  phone: props.owner.profile.phone,
  name: props.owner.profile.name,
  birthday: props.owner.profile.birthday,
  gender: props.owner.profile.gender,
  address: props.owner.profile.address,
})

const userProfile = reactive([])
Object.assign(userProfile, props.owner)
const errors = reactive([])

// Đổi email, sdt
async function changeEmailAndPhone() {
  // Nếu thông tin khác ban đầu thì chuyển
  if (form.email == userProfile.email && form.phone == userProfile.profile.phone) return

  errors.email = validate.email(form.email)
  errors.phone = validate.phone(form.phone)
  //  Nếu không có lỗi
  const hasErrors = Object.values(errors).some((error) => error && error != '')
  console.log(form)
  if (!hasErrors) {
    try {
      const res = await axios.post(`/changeEmailAndPhone`, form)
      toast.success(res.data.message, {
        position: 'bottom-right',
      })

      userProfile.email = res.data.email
      userProfile.email_active = res.data.email_active
      userProfile.profile.phone = res.data.phone
      window.bootstrap.Modal.getInstance(infoModal.value).hide()
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

// Đổi tên
async function changeName() {
  // Nếu thông tin khác ban đầu thì chuyển
  if (form.name == userProfile.profile.name) return
  errors.name = validate.name(form.name)

  //  Nếu không có lỗi
  const hasErrors = Object.values(errors).some((error) => error && error != '')
  if (!hasErrors) {
    try {
      const res = await axios.post(`/changeName`, form)
      toast.success(res.data.message, {
        position: 'bottom-right',
      })
      userProfile.profile.name = res.data.name
      window.bootstrap.Modal.getInstance(nameModal.value).hide()
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

// Đổi ngày sinh
async function changeBirthday() {
  // Nếu thông tin khác ban đầu thì chuyển
  if (form.birthday == userProfile.profile.birthday) return
  errors.birthday = validate.birthday(form.birthday)

  //  Nếu không có lỗi
  const hasErrors = Object.values(errors).some((error) => error && error != '')
  if (!hasErrors) {
    try {
      const res = await axios.post(`/changeBirthday`, form)
      toast.success(res.data.message, {
        position: 'bottom-right',
      })
      userProfile.profile.birthday = res.data.birthday
      window.bootstrap.Modal.getInstance(birthdayModal.value).hide()
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

// Đổi ngày sinh
async function changeGender() {
  // Nếu thông tin khác ban đầu thì chuyển
  if (form.gender == userProfile.profile.gender) return
  errors.gender = validate.gender(form.gender)

  //  Nếu không có lỗi
  const hasErrors = Object.values(errors).some((error) => error && error != '')
  if (!hasErrors) {
    try {
      const res = await axios.post(`/changeGender`, form)
      toast.success(res.data.message, {
        position: 'bottom-right',
      })
      userProfile.profile.gender = res.data.gender
      window.bootstrap.Modal.getInstance(genderModal.value).hide()
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

// Đổi địa chỉ
async function changeAddress() {
  // Nếu thông tin khác ban đầu thì chuyển
  if (form.address == userProfile.profile.address) return
  errors.address = validate.address(form.address)

  // Nếu không có lỗi
  const hasErrors = Object.values(errors).some((error) => error && error != '')
  if (!hasErrors) {
    try {
      const res = await axios.post(`/changeAddress`, form)
      toast.success(res.data.message, {
        position: 'bottom-right',
      })
      userProfile.profile.address = res.data.address
      window.bootstrap.Modal.getInstance(addressModal.value).hide()
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

// Route qua active email
function activeEmail() {
  window.bootstrap.Modal.getInstance(infoModal.value).hide()
}
</script>

<style scoped>
.settingUserInfo {
  margin-top: 2rem;
  margin-bottom: 5rem;
  background-color: var(--main-extra-bg);
  border-radius: 1rem;
  padding: 2rem 3rem;
  min-height: fit-content;
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.1rem;
}

.settingUserInfo__title {
  font-size: 1.4rem;
  font-weight: 600;
  margin-bottom: 2rem;
}

.settingUserInfo__navWrapper__nav:first-child {
  border-top-left-radius: 1rem;
  border-top-right-radius: 1rem;
}

.settingUserInfo__navWrapper__nav--last {
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
}

.settingUserInfo__navWrapper__nav {
  position: relative;
  display: flex;
  flex-direction: column;
  padding: 0.4rem;
  border: 1px solid var(--extra-bg);
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.05rem;
}

.settingUserInfo__navWrapper__nav:hover {
  background-color: var(--hover-color);
  cursor: pointer;
}

.settingUserInfo__navWrapper__next {
  position: absolute;
  right: 2rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.2rem;
}

.settingUserInfo__navWrapper__nav__title {
  font-size: 1rem;
  font-weight: 550;
  margin-bottom: 0.1rem;
  margin-left: 0.4rem;
}

.settingUserInfo__navWrapper__nav__info {
  margin-left: 1rem;
  font-weight: 350;
}

.modal-content {
  margin-top: 5rem;
}
</style>
