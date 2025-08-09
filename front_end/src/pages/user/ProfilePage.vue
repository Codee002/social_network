<template>
  <div v-if="user && !loading && user.status == 'actived'" :key="$route.fullPath">
    <div class="profile__header">
      <div class="profile__inner profile__inner--header">
        <profile-cover :srcThumb="srcThumb" :relationStatus="relationStatus" :user="user"></profile-cover>
        <profile-overview
          :user="user"
          :relationStatus="relationStatus"
          :relation="relation"
          :action="action"
          @addRelation="addRelation"
          @changeRelation="changeRelation"
          @changeMode="changeMode"
          :owner="owner"
        ></profile-overview>
        <profile-nav :mode="mode" @changeMode="changeMode"></profile-nav>
      </div>
    </div>

    <profile-content
      v-if="mode == 'all' && user"
      :user="user"
      :owner="owner"
      :relationStatus="relationStatus"
      @changeMode="changeMode"
      :listFriend="listFriend"
    ></profile-content>
    <friend-page :relationStatus="relationStatus" :listFriend="listFriend" v-if="mode == 'friend'"></friend-page>
    <profile-story v-if="mode == 'story'" :owner="owner"></profile-story>
    <interview-component
      :showListFriend="false"
      @changeMode="changeMode"
      :relationStatus="relationStatus"
      :user="user"
      style="margin: auto; width: 60%"
      v-if="mode == 'intro'"
    ></interview-component>
    <profile-share :user="user" :owner="owner" v-if="mode == 'share'"></profile-share>
    <div style="min-height: 200vh" class="content"></div>
  </div>
  <div v-else-if="user && user.status == 'disabled'">
    <div class="post__media">
      <div class="p-4 d-flex flex-column align-items-center justify-content-center">
        <i class="fa-solid fa-ban text-danger" style="font-size: 20rem"></i>
        <h6 class="text-center mt-3">Trang cá nhân có thể không tồn tại hoặc đã bị khóa</h6>
      </div>
    </div>
  </div>

  <div class="spinner-border" v-else style="margin: auto"></div>
</template>

<script setup>
import { computed, onMounted, ref, watch, defineProps } from 'vue'
import ProfileCover from './profiles/ProfileCover.vue'
import ProfileOverview from './profiles/ProfileOverview.vue'
import ProfileNav from './profiles/ProfileNav.vue'
import ProfileContent from './profiles/ProfileContent.vue'
import FriendPage from '@/pages/user/FriendPage.vue'
import ProfileShare from '@/pages/user/profiles/ProfileShare.vue'
import InterviewComponent from '@/pages/user/profiles/InterviewComponent.vue'
import axios from 'axios'
import auth from '@/utils/auth'
import { useRoute } from 'vue-router'
import ProfileStory from '@/pages/user/profiles/ProfileStory.vue'

const props = defineProps({
  owner: {
    default: [],
  },

  listFriend: {
    default: [],
  },
})

const srcThumb = computed(() => {
  if (!user.value.profile.thumb) {
    return require('@/assets/images/thumb/default.png')
  }
  return user.value.profile.thumb
})

let mode = ref('all')
const route = useRoute()
const profileId = route.params.user_id

const user = ref(null)
const loading = ref(true)
const relation = ref()
const relationStatus = ref('owner')
const action = ref('')

onMounted(async () => {
  try {
    let res = await axios.get(`/user/${profileId}`)
    user.value = res.data.user

    // Nếu người đang xem không phải chủ tài khoản
    if (props.owner.id != user.value.id) {
      res = await axios.get(`/getRelation/${props.owner.id}/${profileId}`)
      relation.value = res.data.relation
      relationStatus.value = auth.getRelationStatus(relation.value)
    }

    // Lấy bạn bè
    res = await axios.get(`/getFriends/${user.value.id}`)
    user.value.friends = res.data.friendList

    // Nhận lời mời
    window.Echo.private(`user.${props.owner.id}`)
      .listen('.receive.relation', (e) => {
        relation.value = e.relation
        console.log('Broadcast: receive.relation', e.relation)
      })
      .error((error) => {
        console.error('Echo error:', error)
      })

    // Gửi lời mời
    window.Echo.private(`user.${props.owner.id}`)
      .listen('.send.relation', (e) => {
        relation.value = e.relation
        console.log('Broadcast: send.relation', e.relation)
      })
      .error((error) => {
        console.error('Echo error:', error)
      })
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  } finally {
    loading.value = false
  }
})

watch(relation, () => {
  // Trường hợp xóa kết bạn
  if (relation.value == 'stranger') relationStatus.value = 'stranger'

  // Chỉ xét lại khi trang cá nhân đang xem là người gửi hoặc người nhận kb
  if (
    (user.value.id == relation.value.sender_id || user.value.id == relation.value.received_id) &&
    user.value.id != props.owner.id
  ) {
    relationStatus.value = auth.getRelationStatus(relation.value)
    if (relationStatus.value == 'friend_pending') {
      // Phía người gửi (đang xem trang người nhận)
      if (relation.value.sender_id == props.owner.id) action.value = 'sender'
      // Phía ngưởi nhận (đang xem trang người gửi)
      else if (relation.value.sender_id == user.value.id) action.value = 'received'
      // Phía người nhận (đang ở trang của chính họ)
      else if (relation.value.received_id == props.owner.id && user.value.id == props.owner.id)
        relationStatus.value = 'owner'
    }
  }

  // Từ chối, Hủy lời mời kết bạn
  if (relationStatus.value == 'stranger' && props.owner.id == user.value.id) {
    relationStatus.value = 'owner'
  }
})

function changeMode(change) {
  mode.value = change
}

async function addRelation(type, status) {
  try {
    await axios.post(`/relation/addRelation`, {
      received_id: user.value.id,
      type: type,
      status: status,
    })
  } catch (error) {
    console.log('Đã xảy ra lỗi', error)
  }
}

async function changeRelation(relationId, type, status) {
  try {
    await axios.post(`/relation/changeRelation`, {
      relation_id: relationId,
      type: type,
      status: status,
    })
  } catch (error) {
    console.log('Đã xảy ra lỗi', error)
  }
}
</script>


<style scroped>
.profile__header {
  background-color: var(--main-extra-bg);
}

.profile__inner {
  position: relative;
  width: 68.5rem;
  max-width: 100%;
  margin: auto;
}
</style>
