<template>
  <div class="friendList__main row">
    <div class="friendList__header">
      <!-- Tìm kiếm -->
      <div class="friendList__header__title col-4 col-lg-3">Bạn bè</div>
      <div class="friendList__header__search col-4 col-lg-6">
        <form>
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
        </form>
      </div>

      <div class="friendList__header__invite col-4 col-lg-3" v-if="relationStatus == 'owner'">
        <a @click="changeMode('friendInvited')">Lời mời kết bạn</a>
      </div>
    </div>
    <hr />
    <!-- Không có bạn -->
    <div v-if="friendSearch.length == 0 && !search" class="d-flex justify-content-center mt-3">
      <p style="font-size: 1.2rem">Không có bạn bè</p>
    </div>

    <!-- Danh sách bạn bè -->
    <div v-if="friendSearch.length != 0" class="row">
      <div class="col-12 col-lg-6 mb-3" v-for="(friend, index) in friendSearch" :key="index">
        <div class="friendList__main__info">
          <router-link :to="{ name: profile, params: { user_id: friend.user.id } }" class="d-flex">
            <img
              :src="friend.user.profile.avatar ?? require('@/assets/images/avatar/default.jpg')"
              class="friendList__main__info__avt"
              alt=""
            />
            <div class="friendList__main__info__name d-flex align-items-center">
              <p>{{ friend.user.profile.name }}</p>
            </div>
          </router-link>
          <i class="fa-solid fa-caret-down fs-4 friendList__main__info__menu__activeMenu" data-bs-toggle="dropdown"></i>
          <ul class="dropdown-menu friendList__main__info__dropdownMenu" data-bs-theme="dark">
            <li>
              <router-link
                class="dropdown-item"
                :to="{ name: profile, params: { user_id: friend.user.id } }"
                style="color: var(--font-color)"
              >
                Xem trang cá nhân
              </router-link>
            </li>
            <li v-if="relationStatus == 'owner'">
              <hr class="dropdown-divider" />
            </li>
            <li v-if="relationStatus == 'owner'">
              <span
                class="dropdown-item text-danger"
                href="#"
                data-bs-toggle="modal"
                :data-bs-target="'#' + friend.user.id"
              >
                <strong>Hủy kết bạn</strong>
              </span>
            </li>
          </ul>
        </div>
        <!-- Modal -->
        <div class="modal" :id="friend.user.id">
          <div class="modal-dialog">
            <div class="modal-content">
              <!-- Modal Header -->
              <div class="modal-header">
                <h5 class="modal-title">Xóa kết bạn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <!-- Modal body -->
              <div class="modal-body">
                Bạn có chắc chắn xóa kết bạn với
                <strong>{{ friend.user.profile.name }}</strong>
                ?
              </div>

              <!-- Modal footer -->
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button
                  type="submit"
                  @click="unFriend(friend.id, 'friend', 'delete')"
                  class="btn btn-danger"
                  data-bs-dismiss="modal"
                >
                  Xóa
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Không tìm thấy bạn -->
    <div v-if="friendSearch.length == 0 && search" class="d-flex justify-content-center mt-3">
      <p style="font-size: 1.2rem">Chưa tìm thấy bạn bè</p>
    </div>
  </div>
</template>

<script setup>
import { defineProps, ref, defineEmits, computed } from 'vue'
const props = defineProps({
  listFriend: {
    default: [],
  },
  relationStatus: {
    default: 'owner',
  },
})
const search = ref('')

const emit = defineEmits(['changeMode', 'changeRelation'])

const friendSearch = computed(() =>
  props.listFriend.filter((friend) => friend.user.profile.name.toLowerCase().includes(search.value.toLowerCase()))
)

function changeMode(change) {
  emit('changeMode', change)
}

function unFriend(relationId, type, status) {
  emit('changeRelation', relationId, type, status)
}
</script>

<style scoped>
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
