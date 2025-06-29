<template>
  <div class="profile__overview">
    <div class="profile__avt">
      <img :src="srcAvtUser" alt="" />
      <button data-bs-toggle="modal" data-bs-target="#modal__upload--avt" v-if="relationStatus == 'owner'">
        <i class="fa-solid fa-camera"></i>
      </button>
      <div
        class="modal modal__upload modal__upload--avt"
        id="modal__upload--avt"
        tabindex="-1"
        aria-labelledby="modal-label"
        aria-hidden="true"
        v-if="relationStatus == 'owner'"
      >
        <div class="modal-dialog modal-dialog-centered">
          <form class="modal-content" action="/profile/avatar" method="post" enctype="multipart/form-data">
            <div class="modal-header">
              <h5 class="modal-title" id="modal-label">Ảnh đại diện</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="upload__preview--avt">
                <img class="rounded-circle" :src="srcAvtUser" alt="" />
                <input class="upload__avt form-control" type="file" name="avatar" accept="image/*" />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="upload__btn btn upload__btn--secondary upload__reset">Hủy</button>
              <button type="submit" class="upload__btn btn upload__btn--primary upload__save hide">Cập nhật</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="profile__info">
      <h1 class="profile__name">{{ user.profile.name }}</h1>
      <div class="profile__preview">
        <div class="profile__count profile__count--friend" href="#">2 người bạn</div>
        <a class="profile__list profile__list--friend">
          <div class="icon-box"><i class="bi bi-three-dots"></i></div>
          <img class="profile__node profile__node--friend" :src="srcAvtUser" alt="" />
          <img class="profile__node profile__node--friend" :src="srcAvtUser" alt="" />
        </a>
      </div>
    </div>
    <div class="profile__action">
      <!-- Chủ tài khoản -->
      <button
        class="profile__btn profile__btn--primary btn"
        data-bs-toggle="modal"
        data-bs-target="#modal__upload--story"
        v-if="relationStatus == 'owner'"
      >
        <i class="fa-solid fa-plus"></i>
        Thêm vào tin
      </button>
      <button class="profile__btn profile__btn--secondary btn" v-if="relationStatus == 'owner'">
        <i class="fa-solid fa-pen"></i>
        Chỉnh sửa trang cá nhân
      </button>

      <!-- Bạn bè -->
      <button class="profile__btn profile__btn--secondary btn" v-if="relationStatus == 'friend'">
        <i class="fa-solid fa-user-group"></i>
        Bạn bè
      </button>

      <button
        class="profile__btn profile__btn--primary btn"
        v-if="relationStatus == 'friend'"
        @click="startChat(user.id)"
      >
        <i class="fa-solid fa-message"></i>
        Nhắn tin
      </button>

      <!-- Người lạ -->
      <button
        class="profile__btn profile__btn--primary btn"
        v-if="relationStatus == 'stranger'"
        @click="addRelation('friend', 'pending')"
      >
        <i class="fa-solid fa-user-group"></i>
        Thêm bạn bè
      </button>

      <!-- Phản hồi -->
      <button
        class="profile__btn profile__btn--secondary btn"
        v-if="action == 'received' && relationStatus == 'friend_pending'"
        @click="changeRelation(relation.id, 'friend', 'reject')"
      >
        <i class="fa-solid fa-xmark"></i>
        Từ chối
      </button>

      <button
        class="profile__btn profile__btn--primary btn"
        v-if="action == 'received' && relationStatus == 'friend_pending'"
        @click="changeRelation(relation.id, 'friend', 'completed')"
      >
        <i class="fa-solid fa-check"></i>
        Chấp nhận
      </button>

      <!-- Đợi phản hồi -->
      <button
        class="profile__btn profile__btn--secondary btn"
        v-if="action == 'sender' && relationStatus == 'friend_pending'"
        @click="changeRelation(relation.id, 'friend', 'reject')"
      >
        <i class="fa-solid fa-xmark"></i>
        Hủy lời mời
      </button>

      <button
        class="profile__btn profile__btn--primary btn"
        v-if="action == 'sender' && relationStatus == 'friend_pending'"
      >
        <i class="fa-solid fa-check"></i>
        Đã gửi lời mời
      </button>
      <!-- Modal -->
      <div
        class="modal modal__upload modal__upload--story"
        id="modal__upload--story"
        tabindex="-1"
        aria-labelledby="modal-label"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered">
          <form class="modal-content" action="/story/add" method="post" enctype="multipart/form-data">
            <div class="modal-header">
              <h5 class="modal-title" id="modal-label">Tạo tin</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="upload__preview--story">
                <img class="rounded" alt="" />
                <input class="upload__story form-control" type="file" name="story" accept="image/*" />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="upload__btn btn upload__btn--secondary upload__reset">Hủy</button>
              <button type="submit" class="upload__btn btn upload__btn--primary upload__save hide">Cập nhật</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import router from '@/router'
import axios from 'axios'
import { computed, defineProps, defineEmits, ref } from 'vue'

const conversation = ref()
const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
  relationStatus: {
    type: String,
  },
  relation: {},

  action: {
    default: 'owner',
  },
})

const srcAvtUser = computed(() => {
  if (!props.user.profile.avatar) {
    return require('@/assets/images/avatar/default.jpg')
  }
  return props.user.profile.avatar
})

const emit = defineEmits(['addRelation', 'changeRelation'])


async function startChat(userId) {
  try {
    const res = await axios.post('/conversation/find', {
      user_id: userId,
    })
    conversation.value = res.data.conversation
    router.push({ name: 'conversation.chat', params: { conversation_id: conversation.value.id } })
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }
}

function addRelation(type, status) {
  emit('addRelation', type, status)
}

function changeRelation(relationId, type, status) {
  emit('changeRelation', relationId, type, status)
}
</script>

<style scoped>
.profile__overview {
  display: flex;
  width: 100%;
  padding-inline: 2rem;
  padding-bottom: 1rem;
}

.profile__avt {
  position: relative;
  margin-top: -2rem;
}

.profile__avt img {
  width: 11rem;
  aspect-ratio: 1 / 1;
  border-radius: 50%;
  border: 0.25rem solid var(--main-bg);
  object-fit: cover;
}

.profile__avt > button {
  position: absolute;
  bottom: 0.75rem;
  right: 0.75rem;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 2.25rem;
  aspect-ratio: 1 / 1;
  color: var(--font-color);
  font-weight: 600;
  border: none;
  border-radius: 50%;
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.1rem 0.1rem;
  background-color: var(--extra-bg);
}

.profile__name {
  font-size: 2rem;
  font-weight: 700;
  line-height: 1.1875rem;
  width: max-content;
  min-width: 100%;
}

.profile__preview {
  padding-top: 0.375rem;
  color: var(--font-extra-color);
  width: max-content;
  min-width: 100%;
}

.profile__count {
  color: inherit;
  font-weight: 600;
  font-size: 0.9375rem;
  line-height: 1.25rem;
}

.profile__list {
  display: flex;
  cursor: pointer;
  margin-top: 0.375rem;
  margin-left: -0.2rem;
  transform: scaleX(-1);
  justify-content: flex-end;
  width: max-content;
}

.profile__list div,
.profile__list img {
  width: 2.2rem;
  aspect-ratio: 1 / 1;
  margin-left: -0.4rem;
  border-radius: 100%;
  border: 0.15rem solid var(--main-extra-bg);
  background-color: var(--main-bg);
  transform: scaleX(-1);
}

.profile__info {
  display: block;
  padding-top: 2rem;
  padding-bottom: 1rem;
  padding-inline: 0.8rem;
}

.profile__action {
  display: flex;
  gap: 0.5rem;
  align-items: flex-end;
  justify-content: flex-end;
  width: 100%;
  padding-bottom: 0.875rem;
}

.profile__btn--primary,
.profile__btn--primary:hover,
.profile__btn--primary:active {
  color: #fff;
  background-color: var(--main1-color);
}

.profile__btn--secondary,
.profile__btn--secondary:hover,
.profile__btn--secondary:active {
  color: var(--font-color);
  background-color: var(--extra-bg);
}

.modal-content {
  background-color: var(--main-extra-bg) !important;
}

.modal-header {
  top: 0;
  width: 100%;
  justify-content: center;
  background-color: var(--main-extra-bg);
}

.modal-close {
  position: absolute;
  padding: 0;
  left: 1rem;
  color: var(--font-color);
}

.upload__btn--secondary {
  background-color: var(--extra-bg);
  color: var(--font-color);
}

.upload__btn--primary {
  background-color: var(--main1-color);
  color: #fff;
}
</style>
