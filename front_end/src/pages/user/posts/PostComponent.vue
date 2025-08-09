<template>
  <div class="post-card">
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
        <a @click="toggleMenu" class="post__more rounded-circle icon-box">
          <i class="fa-solid fa-ellipsis-vertical"></i>
        </a>

        <ul class="dropdown-menu menu__post show" v-if="isMenu">
          <!-- Xem -->
          <router-link :to="{ name: 'post', params: { post_id: post.id } }" class="d-flex">
            <nav-component>
              <template v-slot:icon>
                <i class="fa-solid fa-eye"></i>
              </template>
              <template v-slot:des>Xem bài viết</template>
            </nav-component>
          </router-link>

          <!-- Gửi tin nhắn -->
          <a href="#" data-bs-toggle="modal" data-bs-target="#modal__newchat" class="d-flex">
            <nav-component>
              <template v-slot:icon>
                <i class="fa-solid fa-message"></i>
              </template>
              <template v-slot:des>Gửi trong tin nhắn</template>
            </nav-component>
          </a>

          <hr />
          <!-- Tố cáo -->
          <a href="#" class="d-flex" data-bs-toggle="modal" data-bs-target="#modal__report">
            <nav-component>
              <template v-slot:icon>
                <i class="fa-solid fa-flag text-danger"></i>
              </template>
              <template v-slot:des><p class="text-danger">Tố cáo</p></template>
            </nav-component>
          </a>

          <!-- Xóa bài viết -->
          <a
            href="#"
            class="d-flex"
            v-if="owner.id == post.user_id"
            data-bs-toggle="modal"
            data-bs-target="#modal__remove__post"
          >
            <nav-component>
              <template v-slot:icon>
                <i class="fa-solid fa-xmark text-danger"></i>
              </template>
              <template v-slot:des><p class="text-danger">Xóa bài viết</p></template>
            </nav-component>
          </a>
        </ul>

        <!-- Modal gửi tin nhắn -->
        <div class="modal" id="modal__newchat" tabindex="-1" aria-labelledby="modal-label" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modal-label">Gửi bài viết</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <!-- Bạn bè -->
                <div class="form-floating mb-2 mt-2">
                  <p class="d-contents">Bạn bè:</p>
                  <input type="text" v-model="searchFriendInput" placeholder="Tìm kiếm..." />
                </div>
                <div class="d-flex align-items-center flex-wrap" v-if="selectedUsers.length != 0" style="padding: 1rem">
                  <div class="ms-3 mb-2" v-for="user in selectedUsers" :key="user.id">
                    <img
                      :src="
                        user.profile.avatar
                          ? $backendBaseUrl + user.profile.avatar
                          : require('@/assets/images/avatar/default.jpg')
                      "
                      class="avatar me-3"
                      alt=""
                    />
                    <p class="conver__header__title">{{ user.profile.name }}</p>
                  </div>
                </div>
                <hr />

                <!-- Danh sách bạn bè -->
                <div class="list__friend__container" v-if="friendSearch.length != 0">
                  <div class="list__friend" v-for="relation in friendSearch" :key="relation.id">
                    <img
                      :src="
                        relation.user.profile.avatar
                          ? $backendBaseUrl + relation.user.profile.avatar
                          : require('@/assets/images/avatar/default.jpg')
                      "
                      class="avatar me-3"
                      alt=""
                    />
                    <p class="conver__header__title">{{ relation.user.profile.name }}</p>
                    <input
                      type="checkbox"
                      class="input__check form-check-input"
                      v-model="selectedUsers"
                      :value="relation.user"
                    />
                  </div>
                </div>
                <div class="list__friend__container" v-else>
                  <p class="text-center">Không tìm thấy bạn bè</p>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="upload__btn btn upload__btn--secondary" data-bs-dismiss="modal">
                  Hủy
                </button>
                <button
                  type="submit"
                  class="upload__btn btn upload__btn--primary"
                  :disabled="selectedUsers.length < 1"
                  @click="sendMessage(post.id)"
                  data-bs-dismiss="modal"
                >
                  Gửi
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal tô cáo -->
        <div class="modal" id="modal__report" tabindex="-1" aria-labelledby="modal-label" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modal-label">Tố cáo bài viết</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <!-- Bạn bè -->
                <div class="form-floating mb-2 mt-2">
                  <p class="d-contents">Lý do:</p>
                  <textarea type="text" rows="5" v-model="reportInput" placeholder="Lý do tố cáo..." />
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="upload__btn btn upload__btn--secondary" data-bs-dismiss="modal">
                  Hủy
                </button>
                <button
                  type="submit"
                  class="upload__btn btn btn-danger"
                  :disabled="reportInput.length < 1"
                  @click="reportPost"
                  data-bs-dismiss="modal"
                >
                  Gửi tố cáo
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal xóa bài viết -->
        <div class="modal" id="modal__remove__post" tabindex="-1" aria-labelledby="modal-label" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modal-label">Xóa bài viết</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="p-3 mb-2 mt-2">
                  <p>Khi xóa bài viết thì không thể khôi phục. Bạn chắc chắn với quyết định này?</p>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="upload__btn btn upload__btn--secondary" data-bs-dismiss="modal">
                  Hủy
                </button>
                <button
                  type="submit"
                  class="upload__btn btn btn-danger"
                  @click="removePost(post.id)"
                  data-bs-dismiss="modal"
                >
                  Xóa bài viết
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-------------------- Nếu bài viết còn bình thường và có đủ quyền để xem -------------------->
    <div v-if="rulePost">
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
          <div
            class="post__share"
            data-bs-toggle="modal"
            :data-bs-target="'#share' + post.id"
            @click="toggleShare"
            v-if="post.rule == 'public'"
          >
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
                <router-link :to="{ name: 'profile', params: { user_id: owner.id } }">
                  <img
                    class="comment__avt post__avt rounded-circle"
                    :src="
                      owner.profile.avatar
                        ? $backendBaseUrl + owner.profile.avatar
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
              <router-link :to="{ name: 'profile', params: { user_id: comment.user_id } }">
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
                  <router-link :to="{ name: 'profile', params: { user_id: comment.user_id } }" class="comment__detail">
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
                  <!-- <li>
                    <div class="dropdown-item">Sửa bình luận</div>
                  </li> -->
                  <li
                    v-if="comment.user_id == owner.id"
                    data-bs-toggle="modal"
                    :data-bs-target="'#modal__remove__post' + comment.id"
                  >
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

            <!-- Modal xóa bình luận -->
            <div
              class="modal"
              :id="'modal__remove__post' + comment.id"
              tabindex="-1"
              aria-labelledby="modal-label"
              aria-hidden="true"
            >
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modal-label">Xóa bình luận</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="p-3 mb-2 mt-2">
                      <p>Bạn chắc chắn xóa bình luận?</p>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="upload__btn btn upload__btn--secondary" data-bs-dismiss="modal">
                      Hủy
                    </button>
                    <button
                      type="submit"
                      class="upload__btn btn btn-danger"
                      @click="removeComment(comment.id)"
                      data-bs-dismiss="modal"
                    >
                      Xóa bình luận
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!------------- Nếu bài viết không có quyền xem ------------------>
    <div v-else>
      <div class="post__media">
        <div class="p-4 d-flex flex-column align-items-center justify-content-center">
          <i class="fa-solid fa-ban text-danger" style="font-size: 20rem"></i>
          <h6 class="text-center mt-3">
            Bài viết đã có thể bị gỡ bỏ hoặc được chia sẻ với quyền riêng tư mà bạn không thể xem
          </h6>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { computed, defineProps, onMounted, ref } from 'vue'
import router from '@/router'
import { useToast } from 'vue-toastification'
import NavComponent from '@/layouts/partials/NavComponent.vue'
// import auth from '@/utils/auth'

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
    // default: 'owner',
  },

  listFriend: {
    default: [],
  },
})

// Quyền xem bài viết
// const relation = ref([])
const relationStatus = ref('')

onMounted(async () => {
  // let res = await axios.get(`/getRelation/${props.owner.id}/${props.post.user_id}`)
  // console.log("GET RELATION")
  // relation.value = res.data.relation
  relationStatus.value = props.relationStatus
  // console.log(relationStatus.value)
})

const rulePost = computed(() => {
  // Nếu bài viết bị khóa thì false
  if (props.post.status == 'disabled') return false

  // Nếu chủ bài viết bị khóa thì false
  if (props.post.user.status == "disabled") return false

  if (props.post.user_id == props.owner.id) return true

  // Nếu bài viết ở riêng tư và người dùng hiện tại không phải chủ bài viết
  if (props.post.rule == 'private') return false

  // Nếu bài viết ở chế độ bạn bè và người dùng hiện tại không phải bạn bè của chủ bài viết
  if (props.post.rule == 'friend') {
    return relationStatus.value == 'friend' ?? false
  }

  return true
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

// Comment
window.Echo.channel(`post.${props.post.id}`)
  .listen('.comment.request', (e) => {
    postComments.value.unshift(e.comment)
    console.log('BROADCAST NEW COMMENT: ', e.comment)
  })
  .error((error) => {
    console.error('Echo error:', error)
  })

window.Echo.channel(`post.${props.post.id}`)
  .listen('.comment.remove', (e) => {
    postComments.value = postComments.value.filter((comment) => comment.id != e.comment.id)
    console.log('BROADCAST REMOVE COMMENT: ', e.comment)
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

const isMenu = ref(false)
// Mở menu
function toggleMenu() {
  isMenu.value = !isMenu.value
}

// -------------- Gửi tin nhắn --------------
const selectedUsers = ref([])
const searchFriendInput = ref('')
const listFriend = ref([])

// Lấy DS Bạn bè
onMounted(async () => {
  Object.assign(listFriend.value, props.listFriend)

  if (listFriend.value.length == 0) {
    // console.log('LẤY DS BẠN BÈ')
    try {
      let res = await axios.get(`/getFriends/${props.owner.id}`)
      listFriend.value = res.data.friendList
    } catch (error) {
      console.log('Không lấy được DS bạn bè!', error)
    }
  }
  // console.log(props.listFriend)
  // console.log('BIEN', listFriend.value)
})

const friendSearch = computed(() =>
  listFriend.value.filter((friend) =>
    friend.user.profile.name.toLowerCase().includes(searchFriendInput.value.toLowerCase())
  )
)

async function sendMessage(postId) {
  let formData = new FormData()
  formData.append('content', window.location.origin + '/post/' + postId)

  // User trong cuộc trò chuyện
  if (selectedUsers.value.length != 0) {
    selectedUsers.value.forEach((user) => {
      formData.append('user[]', user.id)
    })
  }
  try {
    let res = await axios.post(`conversation/sendPostMessage`, formData)
    toast.success(res.data.message, {
      position: 'bottom-right',
    })
  } catch (error) {
    console.log('Gửi tin nhắn thất bại!', error)
    toast.error(error.response.data.message, {
      position: 'bottom-right',
    })
  }
}

// ------------------------------------------

// -------------- Tố cáo ----------------------------
const reportInput = ref('')
async function reportPost() {
  let formData = new FormData()
  formData.append('content', reportInput.value)
  formData.append('post_id', props.post.id)
  formData.append('score', -3)
  formData.append('user_id', props.owner.id)

  try {
    let res = await axios.post(`post/storeReport`, formData)
    toast.success(res.data.message, {
      position: 'bottom-right',
    })
  } catch (error) {
    console.log('Gửi tố cáo thất bại!', error)
    toast.error(error.response.data.message, {
      position: 'bottom-right',
    })
  }
}

//--------------------------------------------------------

// ----------- Xóa bài viết ------------
async function removePost(postId) {
  try {
    let res = await axios.post(`post/removePost`, {
      post_id: postId,
    })
    toast.success(res.data.message, {
      position: 'bottom-right',
    })
  } catch (error) {
    console.log('Xóa bài viết thất bại!', error)
    toast.error(error.response.data.message, {
      position: 'bottom-right',
    })
  }
}
//--------------------------------------------------------

// ----------- Xóa bình luận ------------
async function removeComment(commentId) {
  try {
    let res = await axios.post(`post/removeComment`, {
      comment_id: commentId,
    })
    toast.success(res.data.message, {
      position: 'bottom-right',
    })

    postComments.value = postComments.value.filter((comment) => comment.id != commentId)
  } catch (error) {
    console.log('Xóa bình luận thất bại!', error)
    toast.error(error.response.data.message, {
      position: 'bottom-right',
    })
  }
}
//--------------------------------------------------------
</script>

<style>
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

.menu__post {
  background-color: var(--main-extra-bg);
  width: 22rem;
  top: 2.4rem !important;
  transform: none !important;
  border: none;
  border-radius: 0 0 0.5rem 0.5rem;
  box-shadow: rgba(0, 0, 0, 0.2) -0.1rem 0.5rem 0.5rem 0.25rem;
  right: -13rem !important;
  padding: 0.6rem;
}

.dropdown-toggle::after {
  content: unset !important;
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
  width: 100% !important;
  height: 100% !important;
  object-fit: cover !important;
  border-radius: 0.5rem !important;
  margin: 0 !important;
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

/* Modal gửi tin nhắn */
.post-card .modal .list__friend__container .list__friend {
  position: relative;
  display: flex;
  align-items: center;
  padding: 1rem 1.8rem;
  overflow-y: auto;
  min-height: 100%;
  max-height: 150%;
}

.post-card .modal .list__friend__container .list__friend:hover {
  background-color: var(--hover-color);
}

.post-card .modal .input__check {
  position: absolute;
  right: 1rem;
  top: 1.3rem;
}

.post-card .modal .form-floating {
  padding: 1rem;
  display: flex;
  align-items: flex-start;
}

.post-card .modal .form-floating__thumb {
  display: block;
}

.post-card .modal .form-floating p {
  margin: 0;
  width: 8rem;
  font-weight: 750;
}

.post-card .modal .form-floating input,
.modal .form-floating textarea {
  background-color: unset;
  border: unset !important;
  padding: 0 !important;
}

.modal .form-floating textarea {
  width: 23rem;
}

.post-card .modal .form-floating input:focus-visible,
.modal .form-floating textarea:focus-visible {
  border: none !important;
  box-shadow: none !important;
  outline: none;
}

#modal__newchat .modal-body, #modal__report .modal-body, #modal__remove__post .modal-body {
  padding: 0rem !important;
}

.post-card .modal-body hr {
  width: 100%;
}

.post-card .modal .list__friend__container .list__friend {
  position: relative;
  display: flex;
  align-items: center;
  padding: 1rem 1.8rem;
  overflow-y: auto;
  min-height: 100%;
  max-height: 150%;
}

.conver__user img,
.post-card .modal img {
  width: 2.4rem;
  height: 2.4rem;
  border-radius: 50%;
  margin-right: 0.8rem;
}

.conver__header__title {
  display: contents;
  font-weight: 750;
}
/* --------------- */
</style>
