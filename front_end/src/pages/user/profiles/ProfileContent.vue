<template>
  <div class="profile__content d-flex justify-content-center">
    <interview-component @changeMode="changeMode" :relationStatus="relationStatus" :user="user"></interview-component>
    <div class="profile__post">
      <filter-component v-if="relationStatus == 'owner'" :user="user"></filter-component>
      <action-component v-if="relationStatus == 'owner'" :user="user"></action-component>
      <div class="post__container" v-if="posts">
        <post-component
          class="mb-3"
          v-for="post in posts"
          :key="post.id"
          :post="post"
          :views="views[post.id]"
          :likes="likes[post.id]"
          :comments="comments[post.id]"
          :shares="shares[post.id]"
          :renderAll="false"
          :owner="owner"
          :relationStatus="relationStatus"
          v-show="acceptView(post.rule)"
          :ref="(el) => (postRefs[post.id] = el)"
        ></post-component>
      </div>
    </div>
  </div>
</template>

<script setup>
import InterviewComponent from '@/pages/user/profiles/InterviewComponent.vue'
import ActionComponent from '@/pages/user/profiles/ActionComponent.vue'
import PostComponent from '@/pages/user/posts/PostComponent.vue'
import FilterComponent from '@/pages/user/profiles/FilterComponent.vue'
import { defineProps, onMounted, ref, watchEffect, defineEmits } from 'vue'
import axios from 'axios'

const emit = defineEmits(['changeMode'])
function changeMode(mode) {
  console.log('CONTENT changemode', mode)
  emit('changeMode', mode)
}

const props = defineProps({
  user: {},
  owner: {},
  relationStatus: {},
})

const posts = ref()
const views = ref([])
const likes = ref([])
const shares = ref([])
const comments = ref([])

onMounted(async () => {
  try {
    let res = await axios.get(`/getPosts/${props.user.id}`)
    posts.value = res.data.posts
    views.value = res.data.views
    likes.value = res.data.likes
    comments.value = res.data.comments
    shares.value = res.data.shares

    window.Echo.channel(`profile.${props.user.id}`)
      .listen('.post.create', (e) => {
        console.log('BROADCAST NEW POST: ', e)
        posts.value.unshift(e.post)
        console.log(e.post)
        likes.value[e.post.id] = e.likes.slice()
        views.value[e.post.id] = e.views.slice()
        comments.value[e.post.id] = e.comments.slice()
        shares.value[e.post.id] = e.shares.slice()
      })
      .error((error) => {
        console.error('Echo error:', error)
      })
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }
})

// Xử lý quyền xem bài viết
const acceptView = (rule) => {
  if (props.relationStatus == 'owner') return true
  if (rule == 'private') return false
  if (rule == 'friend' && props.relationStatus != 'friend') return false
  return true
}

// Xử lý phần view
const postRefs = ref({})
const viewTimers = {}

// Xử lý phần view
const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      const postId = entry.target.dataset.postId
      if (entry.isIntersecting) {
        // Nếu người dùng hiện tại chưa xem và chưa có timer thì bắt đầu đếm 5s
        if (!views.value[postId].includes(props.owner.id) && !viewTimers[postId]) {
          viewTimers[postId] = setTimeout(() => {
            storeView(postId, props.owner.id, 1)
            delete viewTimers[postId]
          }, 5000)
        }
      } else {
        // Nếu rời khỏi viewport trước 5s thì hủy timeout
        if (viewTimers[postId]) {
          clearTimeout(viewTimers[postId])
          delete viewTimers[postId]
        }
      }
    })
  },
  {
    threshold: 0.5,
  }
)

// Theo dõi view
watchEffect(() => {
  for (const postId in postRefs.value) {
    const el = postRefs.value[postId]?.$el || postRefs.value[postId]
    if (el) {
      el.dataset.postId = postId
      observer.observe(el)
    }
  }

  // Theo dõi thời giann thhuwjc
  if (posts.value) {
    posts.value.forEach((post) => {
      // Lượt view
      window.Echo.channel(`post.${post.id}`)
        .listen('.view.request', (e) => {
          console.log('BROADCAST NEW VIEW: ', e)
          views.value[e.postId] = e.views
        })
        .error((error) => {
          console.error('Echo error:', error)
        })

      // Comment
      window.Echo.channel(`post.${post.id}`)
        .listen('.comment.request', (e) => {
          comments.value[e.postId].unshift(e.comment)
          console.log('BROADCAST NEW COMMENT: ', e.comment)
        })
        .error((error) => {
          console.error('Echo error:', error)
        })

      // Like
      window.Echo.channel(`post.${post.id}`)
        .listen('.like.request', (e) => {
          console.log('BROADCAST NEW LIKE: ', e)
          likes.value[e.postId] = e.likes
        })
        .error((error) => {
          console.error('Echo error:', error)
        })

      //Share
      //Share
      window.Echo.channel(`post.${post.id}`)
        .listen('.share.request', (e) => {
          console.log('BROADCAST NEW SHARE: ', e)
          shares.value[e.postId] = e.shares
        })
        .error((error) => {
          console.error('Echo error:', error)
        })
    })
  }
})

// Cập nhật view
async function storeView(postId, userId, score) {
  let formData = new FormData()
  formData.append('user_id', userId)
  formData.append('post_id', postId)
  formData.append('score', score)
  try {
    let res = await axios.post(`post/storeView`, formData)
    console.log('Cập nhật view thành công', res)
  } catch (error) {
    console.log('Cập nhật view thất bại!', error)
  }
}
</script>

<style scoped>
.profile__post {
  width: 40rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1rem;
}
</style>
