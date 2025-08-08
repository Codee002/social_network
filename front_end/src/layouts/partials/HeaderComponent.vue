<template>
  <header>
    <div class="d-flex align-items-center" style="height: 100%; width: 100em; margin-right: 4rem">
      <img class="logo" :src="srcLogoImage" />
      <form @submit.prevent="search()" class="d-flex align-items-center" style="height: 100%">
        <!-- <search-component class="search" :name="field" :placeholder="placeholder"></search-component> -->
        <input type="text " placeholder="Tìm kiếm" v-model="inputSerach" class="form-control inputSeach" />
      </form>
    </div>

    <div class="header-nav d-flex align-items-center" style="height: 100%; width: 100em">
      <router-link :to="{ name: 'home' }">
        <icon-nav :isActive="{ 'is-active': $route.name === 'home' }"><i class="fa-solid fa-house"></i></icon-nav>
      </router-link>
      <router-link :to="{ name: 'friend' }">
        <icon-nav :isActive="{ 'is-active': $route.name === 'friend' }">
          <i class="fa-solid fa-user-group"></i>
        </icon-nav>
      </router-link>
      <router-link :to="{ name: 'profile', params: { user_id: user.id } }">
        <icon-nav :isActive="{ 'is-active': $route.name === 'profile' }"><i class="fa-solid fa-user"></i></icon-nav>
      </router-link>
      <router-link :to="{ name: 'favorite' }">
        <icon-nav :isActive="{ 'is-active': $route.name === 'favorite' }"><i class="fa-solid fa-heart"></i></icon-nav>
      </router-link>
      <router-link style="position: relative" :to="{ name: 'conversation' }">
        <icon-nav :isActive="{ 'is-active': $route.matched.some((r) => r.name === 'conversation') }">
          <i class="fa-solid fa-message"></i>
        </icon-nav>
        <!-- <span class="notification-dot" style="top: 0.9rem; right: 2rem"></span> -->
      </router-link>
    </div>

    <!-- User Nav -->
    <div class="info-group dropdown-toggle" style="height: 100%; width: 100em">
      <!-- Notification -->
      <div class="notifi__wrapper">
        <i class="fa-solid fa-bell" @click="toggleNotification"></i>
        <span class="notification-dot" v-if="notifications.some((notif) => notif.status == 'received')"></span>
      </div>

      <ul class="dropdown-menu dropdown__notification show" v-if="isNotificationOpen && user">
        <div class="d-flex justify-content-between">
          <h5 style="font-weight: 700">Thông báo</h5>
          <router-link class="notification__all">Xem tất cả</router-link>
        </div>
        <div>
          <hr />
          <div v-if="notifications.length != 0">
            <div
              v-for="notification in notifications"
              :key="notification.id"
              class="d-flex"
              style="position: relative; cursor: pointer"
              @click="readNotification(notification)"
            >
              <span
                class="notification-dot"
                style="top: 3.4rem; right: 0.5rem"
                v-if="notification.status == 'received'"
              ></span>
              <nav-component>
                <template v-slot:icon>
                  <img
                    class="avatar"
                    :src="
                      notification.userAvatar
                        ? $backendBaseUrl + notification.userAvatar
                        : require('@/assets/images/avatar/default.jpg')
                    "
                    alt=""
                  />
                </template>
                <template v-slot:des>{{ notification.userName }}</template>
                <template v-slot:message>
                  <p style="font-weight: 350">{{ notification.content }}</p>
                  <!-- <br> -->
                </template>
                <template v-slot:time>
                  <br />
                  <p style="font-weight: 350; font-size: 0.9rem">{{ $dayjs(notification.created_at).fromNow() }}</p>
                </template>
              </nav-component>
            </div>
          </div>
          <div v-else class="text-center">
            <span>Bạn chưa có thông báo nào</span>
          </div>
        </div>
      </ul>

      <!-- Setting -->
      <img
        class="avatar"
        :src="
          user.profile.avatar ? $backendBaseUrl + user.profile.avatar : require('@/assets/images/avatar/default.jpg')
        "
        @click="toggleSetting"
        alt=""
      />
      <ul class="dropdown-menu show" v-if="isSettingOpen && user">
        <router-link :to="{ name: 'profile', params: { user_id: user.id } }" class="d-flex">
          <nav-component>
            <template v-slot:icon>
              <img
                class="avatar"
                :src="
                  user.profile.avatar
                    ? $backendBaseUrl + user.profile.avatar
                    : require('@/assets/images/avatar/default.jpg')
                "
                alt=""
              />
            </template>
            <template v-slot:des>{{ user.profile.name }}</template>
          </nav-component>
        </router-link>
        <hr />
        <router-link v-if="user.role == 'admin'" :to="{ name: 'admin.account' }" class="d-flex">
          <nav-component>
            <template v-slot:icon>
              <i class="fa-solid fa-lock"></i>
            </template>
            <template v-slot:des>Trang quản lý</template>
          </nav-component>
        </router-link>

        <router-link :to="{ name: 'setting.info' }" class="d-flex">
          <nav-component>
            <template v-slot:icon>
              <i class="fa-solid fa-gear"></i>
            </template>
            <template v-slot:des>Cài đặt & quyền riêng tư</template>
          </nav-component>
        </router-link>
        <a class="d-flex" @click="logout()">
          <nav-component>
            <template v-slot:icon>
              <i class="fa-solid fa-right-from-bracket"></i>
            </template>
            <template v-slot:des>Đăng xuất</template>
          </nav-component>
        </a>
      </ul>
    </div>
  </header>
</template>

<script setup>
// ----------------------- Import -----------------------
import { ref, defineProps } from 'vue'
// import SearchComponent from './SearchComponent.vue'
import IconNav from './IconNav.vue'
import axios from 'axios'
import NavComponent from './NavComponent.vue'
import router from '@/router'
// import auth from '@/utils/auth.js'

// ----------------------- Var -----------------------
const srcLogoImage = ref(require('@/assets/images/general/logo.png'))
const isSettingOpen = ref(false)
const isNotificationOpen = ref(false)

// ----------------------- Function -----------------------

defineProps({
  user: {
    type: Object,
    require: true,
    default: null,
  },
  notifications: {},
})

// Mở setting
function toggleSetting() {
  isSettingOpen.value = !isSettingOpen.value
  isNotificationOpen.value = false
}

// Mở thông báo
function toggleNotification() {
  isNotificationOpen.value = !isNotificationOpen.value
  isSettingOpen.value = false
}

// Đọc thông báo
async function readNotification(notification) {
  notification.status = 'seen'

  try {
    axios.post(`/readNotification/${notification.id}`)
  } catch (error) {
    console.log('Đã xảy ra lỗi khi đọc thông báo!', error)
  }

  if (notification.type == 'relation') {
    router.push({ name: 'friend' })
  } else if (notification.type == 'post') {
    router.push({ name: 'post', params: { post_id: notification.url_id } })
  } else if (notification.type == 'user') {
    router.push({ name: 'admin.account.report' })
  }

  isNotificationOpen.value = false
}
// Đăng xuất
async function logout() {
  try {
    const res = await axios.post(`/auth/logout`, {})
    localStorage.removeItem('auth_token')
    localStorage.removeItem('owner')
    console.log(res)
    router.push({ name: 'auth.login' })
  } catch (error) {
    console.log('Có lỗi khi đăng xuất! ', error)
  }
}

// Tìm kiếm
const inputSerach = ref()
function search() {
  router.push({ name: 'search', params: { input: inputSerach.value } })
}
</script>

<style scoped>
header {
  position: fixed;
  display: flex;
  top: 0;
  height: 3.6rem;
  border-color: var(--main1-color) !important;
  box-shadow: 0 0 0 0.05rem var(--main1color) !important;
  background-color: var(--main-extra-bg);
  width: 100%;
  justify-content: space-between;
  align-items: center;
  z-index: 100;
  border-bottom: 0.01rem solid var(--border-color) !important;
}

header .logo {
  height: 70%;
  margin: 0 0.8rem;
}

header .avatar {
  width: 10%;
  border-radius: 2rem;
  cursor: pointer;
}

.header-nav {
  display: flex;
  width: 40rem;
  justify-content: space-evenly;
  align-items: center;
  gap: 1rem;
}

.info-group {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: end;
  margin: 1rem;
}

.info-group i {
  cursor: pointer;
  font-size: 1.4rem;
  margin-right: 1rem;
  transition: 0.2s;
  color: v-bind('isNotificationOpen ? "var(--main1-color)" : "var(--font-color)"');
}

.info-group i:hover {
  color: var(--main1-color);
}

.dropdown-menu {
  background-color: var(--main-extra-bg);
  width: 22rem;
  top: 3.4rem !important;
  transform: none !important;
  border: none;
  border-radius: 0 0 0.5rem 0.5rem;
  box-shadow: rgba(0, 0, 0, 0.1) -0.1rem 0.5rem 0.5rem 0.25rem;
  left: 6rem !important;
  padding: 0.6rem;
}

.dropdown-toggle::after {
  content: unset !important;
}

.dropdown__notification {
  overflow-y: auto;
  min-height: 7rem;
  max-height: 35rem;
  padding: 1rem;
}

.dropdown__notification .notification__all {
  color: var(--main1-color);
}

.notifi__wrapper {
  position: relative;
  width: fit-content;
}

.notification-dot {
  position: absolute;
  width: 0.4rem;
  height: 0.4rem;
  right: 1.1rem;
  color: red;
  background-color: red;
  border-radius: 50%;
}

.notification-count {
  position: absolute;
  color: red;
  font-size: 0.9rem;
  font-weight: 700;
}

.inputSeach {
  border: unset !important;
  height: 70%;
  border-radius: 2rem !important;
  padding: 1rem;
  width: 17rem;
  background-color: var(--extra-bg) !important;
}
</style>
