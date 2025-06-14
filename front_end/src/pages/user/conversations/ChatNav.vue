<template>
  <div class="sidebar" v-if="conversations">
    <div
      class="user"
      v-for="(conversation, index) in conversations"
      :key="index"
      :class="{ active: $route.params.conversation_id == conversation.id }"
    >
      <router-link :to="{ name: 'conversation.chat', params: { conversation_id: conversation.id } }">
        <nav-component :isHoverable="false">
          <template v-slot:icon>
            <img
              class="avatar"
              :src="conversation.user.avatar ?? require('@/assets/images/avatar/default.jpg')"
              alt=""
            />
          </template>
          <template v-slot:des>
            <p>{{ conversation.user.name }}</p>
          </template>
          <template v-slot:message><p style="font-weight: 350">Hồ sent a sticker... 1w</p></template>
        </nav-component>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import NavComponent from '@/layouts/partials/NavComponent.vue'
import { defineProps } from 'vue'

defineProps({
  conversations: {
    type: Object,
    default: null,
  },
})
</script>

<style scoped>
.sidebar {
  width: 25rem;
  border-right: 0.01rem solid #333;
  overflow-y: auto;
  max-height: 100%;
  min-height: 100%;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE và Edge */
}
.user {
  cursor: pointer;
}

.user:hover {
  background-color: var(--hover-color);
}

.user.active {
  background-color: var(--extra-bg);
  /* border-radius: .1rem; */
}
.user img {
  width: 2.4rem;
  height: 2.4rem;
  border-radius: 50%;
  margin-right: 0.8rem;
}
</style>
