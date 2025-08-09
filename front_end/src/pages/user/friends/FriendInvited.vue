<template>
  <div class="friendList__main row">
    <div class="friendList__header row">
      <div class="friendList__header__title col-4 col-lg-3">Lời mời kết bạn</div>

      <div class="friendList__header__invite col-4 col-lg-3 offset-lg-6">
        <a @click="changeMode('friendList')">Danh sách bạn bè</a>
      </div>
    </div>

    <hr class="mb-3" />

    <!-- Danh sách lời mời-->
    <div v-if="listInvited.length == 0" class="d-flex justify-content-center mt-3">
      <p style="font-size: 1.2rem">Không có lời mời kết bạn</p>
    </div>
    <div v-else>
      <div v-for="relation in listInvited" :key="relation.id" class="col-12 col-lg-6 col-xl-4 col-xxl-3 mb-4">
        <router-link :to="{ name: 'profile', params: { user_id: relation.user.id } }" :key="relation.id">
          <div class="friendInvite__main__info">
            <img
              :src="relation.user.profile.avatar
                  ? $backendBaseUrl + relation.user.profile.avatar
                  : require('@/assets/images/avatar/default.jpg')
              "
              class="friendInvite__main__info__avt"
              alt=""
            />
            <div class="friendInvite__main__info__name">
              <p class="mb-4">{{ relation.user.profile.name }}</p>

              <div class="friendInvite__main__info__option">
                <button
                  @click.stop.prevent="changeRelation(relation.id, 'friend', 'completed')"
                  class="btn mb-2 friendInvite__main__info__option--accept"
                >
                  Chấp nhận
                </button>
                <button
                  @click.stop.prevent="changeRelation(relation.id, 'friend', 'reject')"
                  class="btn btn-secondary friendInvite__main__info__option--ignore"
                >
                  Từ chối
                </button>
              </div>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
defineProps({
  srcAvtUser: {
    type: String,
  },

  listInvited: {},
})

const emit = defineEmits(['changeMode', 'changeRelation'])
function changeMode(change) {
  emit('changeMode', change)
}

function changeRelation(relationId, type, status) {
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

.friendInvite__main__info {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 90%;
  border: 1px solid var(--extra-bg);
  border-radius: 0.8rem;
  box-shadow: 0.01rem 0.1rem 0.3rem 0.015rem rgba(0, 0, 0, 0.15);
}

.friendInvite__main__info:hover {
  background-color: var(--hover-color);
}

.friendInvite__main__info__avt {
  max-width: 100%;
  border-top-left-radius: 0.8rem;
  border-top-right-radius: 0.8rem;
}

.friendInvite__main__info__name {
  display: flex;
  flex-direction: column;
  justify-content: start;
  font-weight: 500;
  padding: 0.8rem;
  font-size: 1rem;
  width: 100%;
}

.friendInvite__main__info__option {
  display: flex;
  flex-direction: column;
  margin-top: -1rem;
}

.friendInvite__main__info__option--accept {
  background-color: var(--main1-color) !important;
  color: #fff;
}

.friendInvite__main__info__option a {
  display: block;
  align-self: center;
  width: 100%;
  flex-wrap: nowrap;
  font-weight: 500;
  font-size: 1rem;
  padding: 0.4rem;
}
</style>
