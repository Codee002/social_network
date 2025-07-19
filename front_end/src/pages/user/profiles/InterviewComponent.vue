<template>
  <div class="profile__inner profile__inner--content">
    <div class="profile__detail">
      <div class="profile__card profile__card--intro">
        <div class="profile__card__header">
          <div class="profile__card__heading">Giới thiệu</div>
        </div>
        <div class="profile__card__content">
          <div class="profile__card__txt profile__card__txt--bio text-center">{{ user.profile.bio ?? '' }}</div>

          <div class="profile__card__btn profile__card__btn--edit" v-if="relationStatus == 'owner'">
            Chỉnh sửa tiểu sử
          </div>
          <div class="profile__card__txt" v-if="user.profile.gender">
            <i class="bi bi-person-square me-2"></i>
            Giới tính:&nbsp; {{ user.profile.gender == "male" ? "Nam" : "Nữ" }}
          </div>

          <div class="profile__card__txt" v-if="user.profile.address">
            <i class="bi bi-house-door-fill me-2"></i>
            Sống tại:&nbsp; {{ user.profile.address ?? '' }}
          </div>

          <div class="profile__card__txt" v-if="user.profile.birthday">
            <i class="bi bi-cake2-fill me-2"></i>
            Ngày sinh:&nbsp; {{ user.profile.birthday ? $dayjs(user.profile.birthday).format('DD-MM-YYYY') : '' }}
          </div>

          <div class="profile__card__txt">
            <i class="bi bi-clock-fill me-2"></i>
            Tham gia vào: {{ $dayjs(user.created_at).format('DD-MM-YYYY') ?? '' }}
          </div>

          <router-link :to="{name: 'setting.info'}" v-if="relationStatus == 'owner'">
            <div class="profile__card__btn">Chỉnh sửa chi tiết</div>
          </router-link>
        </div>
      </div>
      <div class="profile__card profile__card--friend">
        <div class="profile__card__header">
          <div class="d-flex justify-content-between align-items-center">
            <div class="profile__card__heading">Bạn bè</div>
            <a class="profile__card__action" @click="changeMode('friend')">Xem tất cả bạn bè</a>
          </div>
          <div class="profile__card__detail">{{ listFriend.length }} người bạn</div>
        </div>
        <div class="profile__card__content row" v-if="listFriend.length != 0">
          <div class="col-4 p-1" v-for="(friend, index) in listFriend" :key="index">
            <router-link :to="{ name: 'profile', params: { user_id: user.id } }">
              <img
                :src="
                  friend.user.profile.avatar
                    ? $backendBaseUrl + friend.user.profile.avatar
                    : require('@/assets/images/avatar/default.jpg')
                "
                alt=""
              />
              <div class="profile__card__txt text-center">{{ friend.user.profile.name }}</div>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps, defineEmits } from 'vue'
const emit = defineEmits(['changeMode'])
const props = defineProps({
  user: {},
  relationStatus: {},
})

function changeMode(mode) {
  emit('changeMode', mode)
}

const listFriend = computed(() => {
  if (props.user.friends.length > 9) return props.user.friends.slice(0, 9)
  return props.user.friends
})
</script>

<style scoped>
.profile__inner--content {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  padding-right: 0rem;
  background-color: var(--main-bg);
}
.profile__inner {
  position: relative;
  width: 27rem;
  max-width: 100%;
  margin: 0;
}

.profile__detail {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  width: 100%;
}

.profile__card {
  color: var(--font-color);
  background-color: var(--main-extra-bg);
  border-radius: 0.75rem;
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.1rem 0.1rem;
  padding-inline: 1rem;
  padding-block: 0.35rem;
}

.profile__card__header {
  padding-block: 0.65rem;
  font-size: 1.0625rem;
  font-weight: 400;
  line-height: 1.25rem;
}

.profile__card__heading {
  font-size: 1.25rem;
  font-weight: 700;
  line-height: 1.5rem;
}

.profile__card__content {
  margin-bottom: 0.65rem;
}

.profile__card__content img {
  width: 100%;
  border-radius: 0.4rem;
}

.profile__card__detail {
  color: var(--font-extra-color);
  line-height: 1.25rem;
  margin-top: 0.25rem;
}

.profile__card__txt {
  font-size: 0.9375rem;
  line-height: 1.4rem;
  font-weight: 600;
  margin-bottom: 0.65rem;
}

.profile__card__txt i {
  font-size: 1.25rem;
  color: var(--font-extra-color);
}

.profile__card__btn {
  height: 2.25rem;
  align-content: center;
  text-align: center;
  border-radius: 0.3rem;
  background-color: var(--extra-bg);
  font-size: 0.9375rem;
  font-weight: 600;
  margin-bottom: 0.65rem;
}

.profile__card__action {
  cursor: pointer;
}

.profile__card__action:hover {
  color: var(--main1-color);
}
</style>
