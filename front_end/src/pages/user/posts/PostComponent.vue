<template>
  <div class="post-card" v-if="acceptView">
    <!-- Header Post -->
    <div class="post__header" ref="headerPostElementElement">
      <div class="post__author">
        <router-link :to="{ name: 'profile', params: { user_id: post.user_id } }">
          <img
            class="post__avt rounded-circle"
            :src="
              post.user.profile.avatar
                ? $backendBaseUrl + post.user.profile.avatar
                : require('@/assets/images/avatar/default.jpg')
            "
            alt=""
          />
        </router-link>
        <div class="post__detail">
          <router-link :to="{ name: 'profile', params: { user_id: post.user_id } }" class="post__name">
            {{ post.user.profile.name }}
          </router-link>
          <div class="post__privacy">
            <span class="me-1">{{ $dayjs(post.created_at).fromNow() }}</span>
            <span class="me-1">•</span>
            <span>
              <i class="fa-solid fa-earth-americas" v-if="post.rule == 'public'"></i>
              <i class="fa-solid fa-user" v-if="post.rule == 'friend'"></i>
              <i class="fa-solid fa-lock" v-if="post.rule == 'private'"></i>
            </span>
          </div>
        </div>
        <router-link :to="{ name: 'post', params: { post_id: post.id } }" class="post__more rounded-circle icon-box">
          <i class="fa-solid fa-ellipsis-vertical"></i>
        </router-link>
      </div>
    </div>

    <!-- Post Title -->
    <div class="post__text" ref="contentPostElement" :class="{ type__text: post.type == 'text' }">
      {{ post.caption }}
    </div>

    <!-- Post Media -->
    <div class="post__media" ref="mediaPostElement" v-if="post.type == 'media' && post.medias.length != 0">
      <div v-if="renderAll == false && post.medias.length > 1">
        <a :href="$backendBaseUrl + post.medias[0].path" v-if="post.medias[0].type == 'image'">
          <img class="post__img" :src="$backendBaseUrl + post.medias[0].path" alt="" />
        </a>
        <video
          v-if="post.medias[0].type == 'video'"
          class="post__img"
          :src="$backendBaseUrl + post.medias[0].path"
          controls
        />
        <router-link :to="{ name: 'post', params: { post_id: post.id } }">
          <div class="post__media__overlay">
            <span>+{{ post.medias.length - 1 }}</span>
          </div>
        </router-link>
      </div>
      <div v-else v-for="media in post.medias" :key="media.id">
        <a :href="$backendBaseUrl + media.path" v-if="media.type == 'image'">
          <img class="post__img" :src="$backendBaseUrl + media.path" alt="" />
        </a>
        <video v-if="media.type == 'video'" class="post__img" :src="$backendBaseUrl + media.path" controls />
      </div>
    </div>

    <!-- Post Footer -->
    <div class="post__footer">
      <div class="post__btn">
        <div class="post__like" @click="toggleLike(post.id)">
          <i class="fa-regular fa-heart fs-4" :class="{ 'fa-solid text-danger': postLikes.includes(owner.id) }"></i>
          <p class="likes">{{ postLikes.length }}</p>
        </div>
        <div class="post__cmt" @click="toggleComment(post.id)">
          <i class="fa-regular fa-comment fa-flip-horizontal fs-4"></i>
          <p class="comments">{{ postComments.length }}</p>
        </div>
        <div class="post__share" data-bs-toggle="modal" :data-bs-target="'#share' + post.id" @click="toggleShare">
          <i class="fa-regular fa-paper-plane"></i>
          <p class="shares">{{ postShares.length }}</p>
        </div>
      </div>

      <div class="post__view">
        <i class="bi bi-eye-fill fs-6"></i>
        <p>
          <span class="views" v-if="renderAll == false">{{ views.length }} views</span>

          <span class="views" v-else>{{ postViews.length }} views</span>
        </p>
      </div>

      <!-- Modal Share -->
      <div
        class="modal"
        :id="'share' + post.id"
        tabindex="-1"
        aria-labelledby="modal-label"
        aria-hidden="true"
        ref="modalElement"
      >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modal-label">Chia sẻ</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
              <button
                type="button"
                data-bs-dismiss="modal"
                class="upload__btn btn upload__btn--secondary upload__reset"
              >
                Hủy
              </button>
              <button
                class="upload__btn btn upload__btn--primary upload__save hide"
                @click="storeShare(post.id)"
                data-bs-dismiss="modal"
              >
                Chia sẻ
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Comment -->
    <div class="post-card comment-container" v-if="renderAll == true">
      <div class="comment-inner" id="comment-section">
        <!-- Form nhập comment User -->
        <div class="d-flex flex-column">
          <!-- Xem trước ảnh, video gửi đi -->
          <div
            v-if="mediaFiles.length"
            class="d-flex flex-wrap align-items-end"
            style="width: 100%; padding: 0.4rem; margin-left: 3rem"
          >
            <div v-for="(file, index) in mediaFiles" :key="index" class="media-item media-preview">
              <i @click="removeFile(index)" class="fa-solid fa-xmark media__deleteBtn"></i>
              <img v-if="file.type.startsWith('image/')" :src="file.url" />
              <video v-else-if="file.type.startsWith('video/')" :src="file.url" />
            </div>
          </div>

          <form @submit.prevent="storeComment()">
            <div class="comment-card post__header px-0">
              <router-link :to="{ name: profile, params: { user_id: owner.id } }">
                <img
                  class="comment__avt post__avt rounded-circle"
                  :src="
                    owner.profile.avatar
                      ? $backendBaseUrl + post.user.profile.avatar
                      : require('@/assets/images/avatar/default.jpg')
                  "
                  alt=""
                />
              </router-link>
              <div class="comment__area post__upload">
                <textarea v-model="content" @input="autoResize" rows="1" placeholder="Viết bình luận..."></textarea>
                <i @click="triggerFileInput" class="fa-solid fa-image"></i>
                <input
                  type="file"
                  ref="fileInput"
                  multiple
                  accept="image/*,video/*"
                  @change="handleFilesChange"
                  style="display: none"
                />
              </div>
              <button type="submit" class="comment__btn icon-box rounded-circle">
                <i class="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </form>
        </div>

        <!-- Hiển thị tiến trình -->
        <div v-if="uploadProgress > 0 && uploadProgress < 100" class="upload-bar">
          <div class="progress" :style="{ width: uploadProgress + '%' }"></div>
          <span>Đang upload: {{ uploadProgress }}%</span>
        </div>

        <!-- Các comment của post -->
        <div class="d-flex flex-column" v-for="comment in postComments" :key="comment.id">
          <div class="comment-card post__header px-0">
            <router-link :to="{ name: profile, params: { user_id: comment.user_id } }">
              <img
                class="comment__avt post__avt rounded-circle"
                :src="
                  comment.user_avatar
                    ? $backendBaseUrl + comment.user_avatar
                    : require('@/assets/images/avatar/default.jpg')
                "
                alt=""
              />
            </router-link>

            <div class="comment__area post__upload">
              <div class="comment__content">
                <router-link :to="{ name: profile, params: { user_id: comment.user_id } }" class="comment__detail">
                  <span style="font-weight: 750; font-size: 0.9rem">{{ comment.user_name }}</span>
                  <span class="me-1 ms-1">•</span>
                  <span style="font-size: 0.9rem">{{ $dayjs(comment.created_at).fromNow() }}</span>
                </router-link>
                <div class="comment__text">{{ comment.content }}</div>
              </div>
            </div>

            <div class="dropend">
              <div
                class="comment__btn icon-box rounded-circle"
                id="dropdown-comment"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                data-bs-auto-close="true"
              >
                <i class="fa-solid fa-ellipsis"></i>
              </div>
              <ul class="dropdown-menu ms-3 me-1" aria-labelledby="dropdown-comment">
                <li>
                  <div class="dropdown-item">Trả lời</div>
                </li>
                <li>
                  <div class="dropdown-item">Sửa bình luận</div>
                </li>
                <li>
                  <div class="dropdown-item">Xóa bình luận</div>
                </li>
              </ul>
            </div>
          </div>

          <!-- Ảnh và video -->
          <div
            v-if="comment.medias.length != 0"
            class="d-flex flex-wrap align-items-end"
            style="width: 100%; padding-left: 3rem"
          >
            <div v-for="(media, index) in comment.medias" :key="index" class="post__comment__media">
              <a :href="$backendBaseUrl + media.path" v-if="media.type == 'image'">
                <img :src="$backendBaseUrl + media.path" />
              </a>
              <a :href="$backendBaseUrl + media.path" v-else-if="media.type == 'video'">
                <video :src="$backendBaseUrl + media.path" controls />
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { computed, defineProps, ref } from 'vue'
import router from '@/router'
import { useToast } from 'vue-toastification'

const toast = useToast()
const props = defineProps({
  post: {},
  views: {},
  likes: {},
  shares: {},
  comments: {},
  renderAll: {},
  owner: {},
  relationStatus: {
    default: 'owner',
  },
})

// Share
const headerPostElementElement = ref()
const contentPostElement = ref()
const mediaPostElement = ref()
const modalElement = ref()

// Comment
const content = ref()
const fileInput = ref()
const mediaFiles = ref([])
const uploadProgress = ref(0)

// Post Info
const postComments = ref([])
const postLikes = ref([])
const postViews = ref([])
const postShares = ref([])

Object.assign(postComments.value, props.comments)
Object.assign(postLikes.value, props.likes)
Object.assign(postViews.value, props.views)
Object.assign(postShares.value, props.shares)

// Xử lý quyền xem
const acceptView = computed(() => {
  return true
})

// Comment
window.Echo.channel(`post.${props.post.id}`)
  .listen('.comment.request', (e) => {
    postComments.value.unshift(e.comment)
    console.log('BROADCAST NEW COMMENT: ', e.comment)
  })
  .error((error) => {
    console.error('Echo error:', error)
  })

// Like
window.Echo.channel(`post.${props.post.id}`)
  .listen('.like.request', (e) => {
    console.log('BROADCAST NEW LIKE: ', e)
    postLikes.value = e.likes
  })
  .error((error) => {
    console.error('Echo error:', error)
  })

//View
window.Echo.channel(`post.${props.post.id}`)
  .listen('.view.request', (e) => {
    console.log('BROADCAST NEW VIEW: ', e)
    postViews.value = e.views
  })
  .error((error) => {
    console.error('Echo error:', error)
  })

//Share
window.Echo.channel(`post.${props.post.id}`)
  .listen('.share.request', (e) => {
    console.log('BROADCAST NEW SHARE: ', e)
    postShares.value = e.shares
  })
  .error((error) => {
    console.error('Echo error:', error)
  })

// Chọn phương tiện
const triggerFileInput = () => {
  fileInput.value.click()
}

// Xử lý phương tiện
const handleFilesChange = (event) => {
  const files = Array.from(event.target.files)
  files.forEach((file) => {
    const url = URL.createObjectURL(file)
    mediaFiles.value.push({
      file,
      url,
      type: file.type,
    })
  })
}

// Xóa ảnh
const removeFile = (index) => {
  mediaFiles.value.splice(index, 1)
}

// Tự chỉnh độ dài
function autoResize(event) {
  const textarea = event.target
  textarea.style.height = 'auto'
  textarea.style.height = textarea.scrollHeight + 'px'
}

// Like bài viết
async function toggleLike(postId = 1) {
  let formData = new FormData()
  formData.append('user_id', props.owner.id)
  formData.append('post_id', postId)
  formData.append('score', 2)
  try {
    let res = await axios.post(`post/storeLike`, formData)
    console.log('Tương tác thành công', res)
  } catch (error) {
    console.log('Tương tác thất bại!', error)
  }
}

// Mở Comment
function toggleComment(postId = 1) {
  if (props.renderAll == false) {
    router.push({ name: 'post', params: { post_id: postId } })
  }
}

// Lưu comment
async function storeComment() {
  if (mediaFiles.value.length == 0 && content.value.trim().length == 0) return
  try {
    const formData = new FormData()
    formData.append('content', content.value ?? '')
    formData.append('post_id', props.post.id)
    formData.append('score', 3)
    formData.append('user_id', props.owner.id)

    // Nếu có media
    if (mediaFiles.value.length != 0) {
      formData.append('type', 'media')
      mediaFiles.value.forEach((media) => {
        formData.append('media[]', media.file)
        console.log('FILE: ', media.file)
        console.log('FORM: ', formData.media)
      })
    } else {
      formData.append('type', 'message')
    }

    content.value = ''
    mediaFiles.value = []
    await axios.post(`post/storeComment`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      onUploadProgress: (e) => {
        const percent = Math.round((e.loaded * 100) / e.total)
        uploadProgress.value = percent
      },
    })
    uploadProgress.value = 0
    toast.success('Bình luận thành công', {
      position: 'bottom-right',
    })
  } catch (error) {
    console.log('Gửi tin nhắn thất bại!', error)
  }
}

// Mở modal share
function toggleShare() {
  if (modalElement.value) {
    const modalBody = modalElement.value.querySelector('.modal-body')
    modalBody.innerHTML = '<p>Bạn muốn chia sẻ bài viết này?<br>Bài viết sẽ được chia sẻ ở chế độ công khai</p>'

    if (headerPostElementElement.value) modalBody.appendChild(headerPostElementElement.value.cloneNode(true))

    if (contentPostElement.value) modalBody.appendChild(contentPostElement.value.cloneNode(true))

    if (mediaPostElement.value) modalBody.appendChild(mediaPostElement.value.cloneNode(true))
  }
}

// Share bài
async function storeShare(postId) {
  let formData = new FormData()
  formData.append('user_id', props.owner.id)
  formData.append('post_id', postId)
  formData.append('score', 5)
  try {
    await axios.post(`post/storeShare`, formData)
    toast.success('Chia sẻ thành công', {
      position: 'bottom-right',
    })
  } catch (error) {
    console.log('Chia sẻ thất bại!', error)
    toast.error('Đã xảy ra lỗi!', {
      position: 'bottom-right',
    })
  }
}
</script>

<style >
.post-card {
  background-color: var(--main-extra-bg);
  border-radius: 0.75rem;
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.1rem 0.1rem;
}

.post__header {
  height: calc(var(--header-heigh) * 1.3);
  padding: 1rem;
}

.post__author {
  display: flex;
  gap: 0.75rem;
  width: 100%;
  height: 100%;
  position: relative;
}

.post__more {
  position: absolute;
  display: flex;
  font-size: 1.2rem;
  right: 0.8rem;
  align-items: start;
  justify-content: end;
}

.post__detail .post__name {
  font-weight: 600;
}
.post__detail .post__privacy {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--font-extra-color);
  position: relative;
}

.post__text {
  padding: 0 1rem;
  margin-bottom: 0.6rem;
}

.post__media {
  position: relative;
}

.post__media__overlay {
  position: absolute;
  background-color: rgba(0, 0, 0, 0.3);
  height: 100%;
  width: 100%;
  top: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.8rem;
  font-weight: 650;
  color: var(--font-color);
}

.post__footer {
  display: flex;
  font-weight: 600;
  height: var(--header-heigh);
  padding-block: 0.25rem;
  margin-inline: 1rem;
  border-top: var(--extra-bg) solid 0.1rem;
}

.post__footer p {
  display: contents;
}

.post__media .post__img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.rounded-circle {
  height: 100%;
  aspect-ratio: 1 / 1;
}

.post__btn {
  display: flex;
  flex-grow: 1;
  gap: 0.5rem;
  padding: 0.4rem;
}

.post__btn > div {
  display: flex;
  flex-basis: 3rem;
  gap: 0.25rem;
  align-items: center;
  justify-content: flex-start;
  cursor: pointer;
}

.post__view {
  display: flex;
  flex-grow: 1;
  gap: 0.25rem;
  justify-content: flex-end;
  align-items: center;
  color: var(--font-extra-color);
  font-size: 0.8rem;
}

/* Comment */
.comment-inner {
  height: 100%;
  border-top: solid 0.1rem var(--extra-bg);
  padding: 0.5rem;
}

.comment-card {
  display: flex;
  gap: 0.5rem;
  padding-block: 0.5rem;
  height: max-content;
  align-items: center;
}

.comment__area {
  display: flex;
  padding-block: 0.5rem;
  padding-inline: 1rem;
  border-radius: 1.5rem;
}

.comment__area textarea {
  background-color: inherit;
  font-size: 0.9375rem;
  font-weight: 600;
  width: 100%;
  border: unset;
}

.comment__area textarea:focus-visible {
  border: unset !important;
  outline: none !important;
}

.post__upload {
  flex-grow: 1;
  display: flex;
  align-items: center;
  border-radius: calc(var(--header-heigh) * 0.6);
  background-color: var(--extra-bg);
}

.post__upload i {
  margin: 0 0.6rem;
  font-size: 1.2rem;
  font-weight: 650;
  cursor: pointer;
}

.post__upload i:hover {
  color: var(--main1-color);
}

.media-preview {
  position: relative;
  border-radius: 0.5rem;
  width: 7rem;
  display: flex;
  align-items: center;
}

.media-preview img,
.media-preview video {
  width: 100%;
}

.media-preview .media__deleteBtn {
  position: absolute;
  right: 0.4rem;
  top: 0.4rem;
  font-size: 1.5rem;
  cursor: pointer;
  z-index: 10;
}

.comment-card > a {
  height: 2.5rem;
}

.comment__btn {
  background-color: inherit;
  height: 2.5rem;
  background-color: var(--extra-bg);
  border: none;
}

.icon-box {
  display: flex;
  aspect-ratio: 1 / 1;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

.upload-bar {
  position: relative;
  background: #eee;
  height: 10px;
  border-radius: 6px;
  margin-top: 10px;
  margin-bottom: 10px;
  width: 80%;
}

.post__comment__media {
  /* width: 10rem; */
  border-radius: 0.5rem;
}

.post__comment__media img,
.post__comment__media video {
  width: 10rem;
  border-radius: 0;
  height: unset;
  border-radius: 1.2rem;
  margin-bottom: 0.2rem;
}
</style>
