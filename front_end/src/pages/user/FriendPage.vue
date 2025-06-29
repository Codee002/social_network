<template>
  <div class="friendList col-12 col-sm-8 offset-sm-2" v-if="owner && listFriend">
    <!-- Danh sách bạn bè -->
    <friend-list
      @changeMode="changeMode"
      :search="search"
      :listFriend="listFriend"
      :relationStatus="relationStatus"
      v-if="mode == 'friendList'"
      @changeRelation="changeRelation"
    ></friend-list>

    <!-- Lời mời kết bạn -->
    <friend-invited
      :listInvited="listInvited"
      @changeMode="changeMode"
      @changeRelation="changeRelation"
      v-if="mode == 'friendInvited'"
    ></friend-invited>
  </div>
  <div class="spinner-border" v-else style="margin: auto"></div>
</template>

<script setup>
import { onMounted, ref, defineProps } from 'vue'
import FriendInvited from '@/pages/user/friends/FriendInvited.vue'
import FriendList from '@/pages/user/friends/FriendList.vue'
import auth from '@/utils/auth'
import axios from 'axios'

defineProps({
  relationStatus: {
    default: 'owner',
  },
})

let mode = ref('friendList')
const owner = ref()
const search = ref()
const listFriend = ref()
const listInvited = ref([])

onMounted(async () => {
  try {
    owner.value = await auth.getOwner()
    let res = await axios.get(`/getFriends/${owner.value.id}`)
    listFriend.value = res.data.friendList

    res = await axios.get(`/getInvited/${owner.value.id}`)
    listInvited.value = res.data.listInvited

    window.Echo.private(`user.${owner.value.id}`)
      .listen('.receive.relation', (e) => {
        console.log('Broadcast: receive.relation', e.friendList)
        listInvited.value = e.listInvited
        listFriend.value = e.friendList
      })
      .error((error) => {
        console.error('Echo error:', error)
      })

    window.Echo.private(`user.${owner.value.id}`)
      .listen('.send.relation', (e) => {
        console.log('Broadcast: send.relation', e.friendList)
        listInvited.value = e.listInvited
        listFriend.value = e.friendList
      })
      .error((error) => {
        console.error('Echo error:', error)
      })
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
    owner.value = null
  }
})

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

function changeMode(change) {
  mode.value = change
}
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
</style>
