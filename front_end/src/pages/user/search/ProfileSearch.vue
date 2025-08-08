<template>
  <div class="friendList col-12 col-sm-8 offset-sm-2" v-if="users.length != 0">
    <div class="friendList__main row">
      <div class="friendList__header">
        <!-- Tìm kiếm -->
        <div class="friendList__header__title col-4 col-lg-3">Tìm kiếm</div>
        <div class="friendList__header__search col-4 col-lg-6">
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input
              class="friendList__header__search-input form-control"
              name="search"
              placeholder="Tìm kiếm..."
              value=""
              v-model="search"
            />
          </div>
        </div>

        <div class="friendList__header__invite col-4 col-lg-3">
          <router-link :to="{ name: 'friend' }">Bạn bè</router-link>
        </div>
      </div>
      <hr />

      <!-- Danh sách người dùng -->
      <div v-if="userSearch.length != 0" class="row">
        <div class="col-12 col-lg-6 mb-3" v-for="(user, index) in userSearch" :key="index">
          <div class="friendList__main__info">
            <router-link :to="{ name: 'profile', params: { user_id: user.id } }" class="d-flex">
              <img
                :src="
                  user.profile.avatar
                    ? $backendBaseUrl + user.profile.avatar
                    : require('@/assets/images/avatar/default.jpg')
                "
                class="friendList__main__info__avt"
                alt=""
              />
              <div class="friendList__main__info__name d-flex align-items-center">
                <p>{{ user.profile.name }}</p>
              </div>
            </router-link>
            <i
              class="fa-solid fa-caret-down fs-4 friendList__main__info__menu__activeMenu"
              data-bs-toggle="dropdown"
            ></i>
            <ul class="dropdown-menu friendList__main__info__dropdownMenu" data-bs-theme="dark">
              <li>
                <router-link
                  class="dropdown-item"
                  :to="{ name: 'profile', params: { user_id: user.id } }"
                  style="color: var(--font-color)"
                >
                  Xem trang cá nhân
                </router-link>
              </li>
              <li v-if="relationStatus == 'owner'">
                <hr class="dropdown-divider" />
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Không tìm thấy người dùng -->
      <div v-if="userSearch.length == 0 && search" class="d-flex justify-content-center mt-3">
        <p style="font-size: 1.2rem">
          Không có kết quả tìm kiếm cho từ khóa
          <b>{{ search }}</b>
        </p>
      </div>
    </div>
  </div>

  <!-- Loading  -->
  <div class="d-flex justify-content-center flex-column align-items-center mb-3" v-else style="margin: auto">
    <div class="spinner-border mt-4"></div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, defineProps, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'

defineProps({
  owner: {},
})

const users = ref([])
const route = useRoute()
const search = ref('')
const tempSearch = computed(() => route.params.input)
search.value = tempSearch.value
watch(
  () => tempSearch.value,
  (newVal) => {
    search.value = newVal
  }
)

onMounted(async () => {
  try {
    let res = await axios.get(`/searchProfile`)
    users.value = res.data.users
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }
})

const userSearch = computed(() =>
  users.value.filter((user) => user.profile.name.toLowerCase().includes(search.value.toLowerCase()))
)
</script>

<style scoped>
.friendList {
  border: 1px solid var(--extra-bg);
  border-radius: 0.5rem;
  background-color: var(--main-extra-bg);
  padding: 2rem 3rem;
  margin-top: 2rem;
}

/*  */
.friendList__main {
  margin-top: 1rem;
  display: flex;
}

/*  */
.friendList__main__info {
  position: relative;
  height: 100%;
  padding: 1rem;
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  border-radius: 1rem;
  margin-bottom: 1rem;
  box-shadow: rgba(0, 0, 0, 0.1) 0.05rem 0.05rem;
}

.friendList__main__info:hover {
  background-color: var(--hover-color);
}

.friendList__main__info__avt {
  width: 5rem;
  border-radius: 1rem;
}

.friendList__main__info__name {
  width: 80%;
  padding: 0 1.2rem;
  font-weight: 550;
  font-size: 1.1rem;
}

.friendList__main__info__menu__activeMenu {
  position: absolute;
  right: 2rem;
  top: 2.5rem;
}

.friendList__main__info__dropdownMenu {
  background-color: var(--main-extra-bg) !important;
  box-shadow: rgba(0, 0, 0, 0.1) 0.05rem 0.05rem;
}

.friendList__header {
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  margin-bottom: 2rem;
}

.friendList__header__title {
  justify-content: start;
  font-size: 1.4rem;
  font-weight: 650;
  /* margin-left: 2rem; */
}

.friendList__header__search {
  display: flex;
  align-items: center;
  justify-content: center;
}

.friendList__header__search form {
  margin: 0;
  display: flex;
  justify-content: center;
  width: 100%;
}

.friendList__header__search .input-group {
  width: 75%;
}

.friendList__header__search-input {
  background-color: var(--main-extra-bg);
  padding: 0.6rem;
  border-radius: 0rem !important;
  border-top-right-radius: 0.6rem !important;
  border-bottom-right-radius: 0.6rem !important;
  border: 1px solid var(--border-color) !important;
}

.friendList__header__search .input-group-text {
  background-color: var(--main-extra-bg) !important;
}

.friendList__header__invite {
  display: flex;
  align-items: center;
  text-align: center;
  justify-content: end;
  font-size: 0.9rem;
  font-weight: 550;
}

.friendList__header__invite a {
  color: #75b6ff;
  cursor: pointer;
}

.friendList__header__invite a:hover {
  color: #75b6ff;
}

.friendList__main__info {
  position: relative;
  height: 100%;
  padding: 1rem;
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  border-radius: 1rem;
  margin-bottom: 1rem;
  box-shadow: rgba(0, 0, 0, 0.1) 0.05rem 0.05rem;
}

.friendList__main__info:hover {
  background-color: var(--hover-color);
}

.friendList__main__info__avt {
  width: 5rem;
  border-radius: 1rem;
}

.friendList__main__info__name {
  width: 80%;
  padding: 0 1.2rem;
  font-weight: 550;
  font-size: 1.1rem;
}

.friendList__main__info__menu__activeMenu {
  position: absolute;
  right: 2rem;
  top: 2.5rem;
}

.friendList__main__info__dropdownMenu {
  background-color: var(--main-extra-bg) !important;
  box-shadow: rgba(0, 0, 0, 0.1) 0.05rem 0.05rem;
}

.modal-footer {
  justify-content: end !important;
  gap: 0rem !important;
}
</style>
