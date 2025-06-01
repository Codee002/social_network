<template>
  <div class="profile__header">
    <div class="profile__inner profile__inner--header">
      <profile-cover :srcThumb="srcThumb"></profile-cover>
      <profile-overview :srcAvtUser="srcAvtUser"></profile-overview>
      <profile-nav :mode='mode' @changeMode="changeMode"></profile-nav>
    </div>
  </div>

  <profile-content v-if="mode == 'all'"></profile-content>
  <friend-page  v-if="mode == 'friend'"></friend-page>
  
  <div style="min-height: 200vh" class="content"></div>
</template>

<script setup>
import { ref } from "vue";
import ProfileCover from "./profiles/ProfileCover.vue";
import ProfileOverview from "./profiles/ProfileOverview.vue";
import ProfileNav from "./profiles/ProfileNav.vue";
import ProfileContent from "./profiles/ProfileContent.vue";
import FriendPage from "@/pages/user/FriendPage.vue";

const srcAvtUser = ref(require("@/assets/images/avatar/default.jpg"));
const srcThumb = ref(require("@/assets/images/thumb/default.png"));
let mode = ref("all");

function changeMode(change) {
  mode.value = change;
  console.log(mode, change);
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
