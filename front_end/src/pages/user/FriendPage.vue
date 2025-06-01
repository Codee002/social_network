<template>
  <div class="friendList col-12 col-sm-8 offset-sm-2">
    <friend-breadcrumb
      :show="show"
      @changeMode="changeMode"
    ></friend-breadcrumb>
    <hr />
    <!-- Danh sách bạn bè -->
    <friend-list
      v-if="mode == 'friendList'"
      :srcAvtUser="srcAvtUser"
    ></friend-list>

    <friend-invited
      :srcAvtUser="srcAvtUser"
      v-if="mode == 'friendInvited'"
    ></friend-invited>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import FriendInvited from "@/pages/user/friends/FriendInvited.vue";
import FriendBreadcrumb from "@/pages/user/friends/FriendBreadcrumb.vue";
import FriendList from "@/pages/user/friends/FriendList.vue";

const srcAvtUser = ref(require("@/assets/images/avatar/default.jpg"));

let mode = ref("friendList");
let show = reactive({});

function changeShow() {
  if (mode.value == "friendList") {
    show.title = "Bạn bè";
    show.navName = "Lời mời kết bạn";
    show.mode = "friendInvited";
    show.form = true;
  } else if (mode.value == "friendInvited") {
    show.title = "Lời mời kết bạn";
    show.navName = "Danh sách bạn bè";
    show.mode = "friendList";
    show.form = false;
  }
}
changeShow();

function changeMode(change) {
  mode.value = change;
  console.log(mode.value);
  changeShow();
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
