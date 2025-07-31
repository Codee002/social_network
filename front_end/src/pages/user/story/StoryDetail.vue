<template>
  <div style="margin: auto" v-if="story">
    <div class="story-card">
      <img class="story__img" :src="$backendBaseUrl + story.media.path" alt="" />
      <div class="story__view" v-if="story.user_id == owner.id">
        <p>Có {{ views.length }} người xem</p>
        <div></div>
      </div>
      <router-link :to="{ name: profile, params: { user_id: story.user_id } }">
        <div
          class="story__avt"
          :class="{ 'story__avt--new': !views.includes(owner.id) }"
          style="height: 3rem; width: 3rem; left: 0.6rem; top: 0.5rem"
        >
          <img
            class="rounded-circle"
            :src="
              story.user.profile.avatar
                ? $backendBaseUrl + story.user.profile.avatar
                : require('@/assets/images/avatar/default.jpg')
            "
            alt=""
          />
        </div>
        <div
          class="story__txt position-absolute"
          style="top: -0.9rem; left: 3rem; font-size: 1.0625rem; font-weight: 600"
        >
          {{ story.user.profile.name }}
        </div>
        <div class="story__txt position-absolute" style="top: 0.4rem; left: 3.1rem">
          {{ $dayjs(story.created_at).fromNow() }}
        </div>
      </router-link>
      <router-link :to="{name: 'home'}" class="position-absolute" style="top: 1.1rem; right: 0.8rem">
        <i class="fa-solid fa-xmark fs-3"></i>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref, defineProps } from 'vue'
import { useRoute } from 'vue-router'
const props = defineProps({
  owner: {},
})
const story = ref()
const views = ref()
const route = useRoute()
const storyId = route.params.story_id

onMounted(async () => {
  try {
    let res = await axios.get(`/story/getStoryDetail/${storyId}`)
    story.value = res.data.story
    views.value = res.data.views
    console.log(views.value)

    if (!views.value.includes(props.owner.id)) {
      await updateView()
    }

    window.Echo.private(`user.${props.owner.id}`)
      .listen('.story.view.request', (e) => {
        console.log('BROADCAST NEW STORY VIEW: ', e)
        views.value = e.views
      })
      .error((error) => {
        console.error('Echo error:', error)
      })
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }
})

async function updateView() {
  let formData = new FormData()
  const score = 1
  formData.append('user_id', props.owner.id)
  formData.append('story_id', storyId)
  formData.append('score', score)
  try {
    await axios.post(`story/storeView`, formData)
    console.log('UPDATE VIEW THANH CONG')
  } catch (error) {
    console.log('Cập nhật view thất bại!', error)
  }
}
</script>

<style scoped>
.story-card {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 34rem;
  min-width: 7.6rem;
  width: 20rem;
  background-color: var(--main-extra-bg);
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.1rem 0.1rem;
  border-radius: 0.75rem;
  overflow: hidden;
  margin: auto;
  margin-top: 1rem;
}

.story__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0.8;
}

.story__avt {
  position: absolute;
  height: 20%;
  aspect-ratio: 1 / 1;
  border-radius: 100%;
  border: 0.3rem solid var(--main-extra-bg);
  box-shadow: rgba(0, 0, 0, 0.1) 0px 2px 8px 0px, rgba(0, 0, 0, 0.1) 0px 0px 0px 1px;
  top: 1rem;
  left: 1rem;
}

.story__txt {
  position: absolute;
  bottom: 0;
  font-size: 0.8rem;
  font-weight: 600;
  width: 100%;
  height: 25%;
  padding-top: 1.5rem;
  padding-left: 1rem;
  color: #fff;
}

.position-absolute {
  position: absolute !important;
}

.story__view {
  position: absolute;
  bottom: 1rem;
  left: 1rem;
}
</style>
