<template>
  <div class="spinner-border" v-if="loading" style="margin: auto"></div>

  <div v-else>
    <div class="profile__header">
      <div class="profile__inner profile__inner--header">
        <profile-cover :srcThumb="srcThumb" :relationStatus="relationStatus"></profile-cover>
        <profile-overview
          :user="user"
          :relationStatus="relationStatus"
          :relation="relation"
          :action="action"
          @addRelation="addRelation"
          @changeRelation="changeRelation"
        ></profile-overview>
        <profile-nav :mode="mode" @changeMode="changeMode"></profile-nav>
      </div>
    </div>

    <profile-content v-if="mode == 'all'"></profile-content>
    <friend-page v-if="mode == 'friend'"></friend-page>

    <div style="min-height: 200vh" class="content"></div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import ProfileCover from './profiles/ProfileCover.vue'
import ProfileOverview from './profiles/ProfileOverview.vue'
import ProfileNav from './profiles/ProfileNav.vue'
import ProfileContent from './profiles/ProfileContent.vue'
import FriendPage from '@/pages/user/FriendPage.vue'
import axios from 'axios'
import { useRoute } from 'vue-router'
import auth from '@/utils/auth'

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
const owner = ref(null)
const loading = ref(true)
const relation = ref('owner')
const relationStatus = ref('owner')
const action = ref('')

onMounted(async () => {
  try {
    owner.value = await auth.getOwner()
    let res = await axios.get(`/user/${profileId}`)
    user.value = res.data.user

    // Nếu người đang xem không phải chủ tài khoản
    if (owner.value.id != user.value.id) {
      res = await axios.get(`/getRelation/${owner.value.id}/${profileId}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
        },
      })
      relation.value = res.data.relation
    }

    window.Echo.private(`user.${owner.value.id}`)
      .listen('.relation.request', (e) => {
        relation.value = e.relation
        console.log('Broadcast: relation.request', e.relation)
      })
      .error((error) => {
        console.error('Echo error:', error)
      })
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
    owner.value = null
  } finally {
    loading.value = false
  }
})

watch(relation, () => {
  friendStatus()
})

function friendStatus() {
  if (relation.value == 'stranger' || relation.value.status == 'reject') {
    relationStatus.value = 'stranger'
  } else if (relation.value.type == 'friend') {
    if (relation.value.status == 'completed') {
      relationStatus.value = 'friend'
    } else {
      relationStatus.value = 'friend_pending'
      if (relation.value.sender_id == owner.value.id) action.value = 'sender'
      else action.value = 'received'
    }
  }
}

function changeMode(change) {
  mode.value = change
  console.log(mode, change)
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
  console.log(relationId, type, status)
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
