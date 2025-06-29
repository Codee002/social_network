<template>
  <div class="post__container" v-if="post">
    <post-component
      class="mb-3"
      :owner="owner"
      :views="views"
      :likes="likes"
      :comments="comments"
      :shares="shares"
      :post="post"
      :renderAll="true"
    ></post-component>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref, defineProps } from 'vue'
import { useRoute } from 'vue-router'
import PostComponent from '@/pages/user/posts/PostComponent.vue'
const route = useRoute()
const postId = route.params.post_id
const post = ref()
const views = ref([])
const likes = ref([])
const shares = ref([])
const comments = ref([])

const props = defineProps({
  owner: {},
})

onMounted(async () => {
  try {
    let res = await axios.get(`/post/detail/${postId}`)
    post.value = res.data.post
    views.value = post.value.views
    likes.value = post.value.likes
    comments.value = post.value.comments
    shares.value = post.value.shares

    // Cập nhật view
    let formData = new FormData()
    formData.append('user_id', props.owner.id)
    formData.append('post_id', postId)
    formData.append('score', 1)
    try {
      let res = await axios.post(`post/storeView`, formData)
      console.log('Cập nhật view thành công', res)
    } catch (error) {
      console.log('Cập nhật view thất bại!', error)
    }

  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }
})
</script>

<style scoped>
.post__container {
  width: 40rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1rem;
  margin: auto;
}
</style>
