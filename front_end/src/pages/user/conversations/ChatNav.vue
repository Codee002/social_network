<template>
  <div class="sidebar" v-if="conversations">
    <!-- NAV -->
    <div class="conver__action">
      <div class="conver__action__header">
        <h5 class="conver__header__title">Tin nhắn</h5>
        <i class="fa-solid fa-pen-to-square new__conver" data-bs-toggle="modal" data-bs-target="#modal__newchat"></i>

        <!-- Modal -->
        <div class="modal" id="modal__newchat" tabindex="-1" aria-labelledby="modal-label" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" action="/story/add" method="post" enctype="multipart/form-data">
              <div class="modal-header">
                <h5 class="modal-title" id="modal-label">Tạo cuộc trò chuyện mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <!-- Tên nhóm -->
                <div class="form-floating mb-2 mt-2">
                  <p class="d-contents">Tên nhóm:</p>
                  <input type="text" placeholder="Tên nhóm..." v-model="nameConversationForm" />
                </div>
                <hr />

                <!-- Ảnh nhóm -->
                <div>
                  <div class="form-floating mb-2 mt-2">
                    <p class="d-contents">Ảnh nhóm:</p>
                    <input type="file" accept="image/*" @change="handleFilesChange" class="d-none" ref="fileInput" />
                    <div class="post__item post__item--image" @click="triggerFileInput">
                      <i class="fa-solid fa-images text-success me-2"></i>
                      <span>Hình ảnh</span>
                    </div>
                  </div>
                  <div v-if="mediaFiles.length" class="">
                    <div
                      v-for="(file, index) in mediaFiles"
                      :key="index"
                      class="media-item media-preview"
                      style="margin-bottom: 10px"
                    >
                      <i @click="removeFile(index)" class="fa-solid fa-xmark media__deleteBtn"></i>
                      <img v-if="file.type.startsWith('image/')" :src="file.url" />
                    </div>
                  </div>
                </div>
                <hr />

                <!-- Thành viên nhóm -->
                <div class="form-floating mb-2 mt-2">
                  <p class="d-contents">Thành viên:</p>
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
                  :disabled="selectedUsers.length < 2 || mediaFiles.length == 0 || nameConversationForm.length == 0"
                  @click="createConversation"
                  data-bs-dismiss="modal"
                >
                  Tạo
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="cover__action__search">
        <input type="text" class="form-control" placeholder="Trò chuyện..." v-model="searchConverInput" />
      </div>
    </div>

    <hr />
    <!-- Các cuộc hội thoại -->
    <div v-if="converSearch.length == 0 && conversations" class="text-center">Không tìm thấy cuộc trò chuyện</div>
    <div v-else>
      <div
        class="conver__user"
        v-for="(conversation, index) in converSearch"
        :key="index"
        :class="{ active: $route.params.conversation_id == conversation.id }"
      >
        <router-link :to="{ name: 'conversation.chat', params: { conversation_id: conversation.id } }">
          <nav-component :isHoverable="false">
            <template v-slot:icon>
              <img class="avatar" :src="thumbs[conversation.id]" alt="" />
            </template>
            <template v-slot:des>
              <p>{{ conversation.name ?? conversation.user.name }}</p>
            </template>
            <template v-slot:message>
              <p style="font-weight: 350">{{ currentMessages[conversation.id] }}</p>
            </template>
          </nav-component>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import NavComponent from '@/layouts/partials/NavComponent.vue'
import router from '@/router'
import axios from 'axios'
import { computed, defineProps, onMounted, ref } from 'vue'
import { useToast } from 'vue-toastification'

const props = defineProps({
  conversations: {
    type: Object,
    default: null,
  },

  owner: {},
  thumbs: {
    default: [],
  },
  currentMessages: {
    default: [],
  },
})

const toast = useToast()

// Tạo cuộc hội thoại
const selectedUsers = ref([])
const searchFriendInput = ref('')
const listFriend = ref([])
const mediaFiles = ref([])
const fileInput = ref()
const nameConversationForm = ref('')
const friendSearch = computed(() =>
  listFriend.value.filter((friend) =>
    friend.user.profile.name.toLowerCase().includes(searchFriendInput.value.toLowerCase())
  )
)

// Tìm cuộc hội thoại
const searchConverInput = ref('')
const converSearch = computed(() => {
  return props.conversations.filter((conversation) => {
    if (conversation.type == 'group') {
      return conversation.name?.toLowerCase().includes(searchConverInput.value.toLowerCase())
    } else if (conversation.type == 'friend') {
      return conversation.user?.name?.toLowerCase().includes(searchConverInput.value.toLowerCase())
    }
    return []
  })
})

onMounted(async () => {
  try {
    let res = await axios.get(`/getFriends/${props.owner.id}`)
    listFriend.value = res.data.friendList
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }
})

// Ảnh nhóm
const handleFilesChange = (event) => {
  mediaFiles.value = []
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

const removeFile = (index) => {
  mediaFiles.value.splice(index, 1)
}

const triggerFileInput = () => {
  fileInput.value.click()
}

// Tạo nhóm
async function createConversation() {
  let formData = new FormData()
  formData.append('name', nameConversationForm.value)
  formData.append('thumb', mediaFiles.value[0].file)
  formData.append('type', 'group')

  // User trong cuộc trò chuyện
  if (selectedUsers.value.length != 0) {
    selectedUsers.value.forEach((user) => {
      formData.append('user[]', user.id)
      formData.append('role[]', 'member')
      formData.append('has_created[]', 0)
    })
  }

  // Thêm bản thân vào
  formData.append('user[]', props.owner.id)
  formData.append('role[]', 'admin')
  formData.append('has_created[]', 1)

  try {
    let res = await axios.post(`conversation/createConversation`, formData)
    console.log('Tạo cuộc trò chuyện thành công', res.data)
    toast.success(res.data.message, {
      position: 'bottom-right',
    })
    router.push({ name: 'conversation.chat', params: { conversation_id: res.data.conversation.id } })
  } catch (error) {
    console.log('Tạo cuộc trò chuyện thất bại!', error)
    toast.error(error.response.data.message, {
      position: 'bottom-right',
    })
  }
}
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

.conver__action {
  display: flex;
  flex-direction: column;
  padding: 1rem;
}

.conver__action__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.conver__header__title {
  display: contents;
  font-weight: 750;
}

.new__conver {
  font-size: 1.3rem;
  font-weight: 750;
  cursor: pointer;
}

.new__conver:hover {
  color: var(--main1-color);
}

.cover__action__search input {
  background-color: var(--main-extra-bg) !important;
  border: unset !important;
  height: 3rem;
}

/* Modal */
.modal .list__friend__container .list__friend {
  position: relative;
  display: flex;
  align-items: center;
  padding: 1rem 1.8rem;
  overflow-y: auto;
  min-height: 100%;
  max-height: 150%;
}

.modal .list__friend__container .list__friend:hover {
  background-color: var(--hover-color);
}

.modal .input__check {
  position: absolute;
  right: 1rem;
  top: 1.3rem;
}

.form-floating {
  padding: 1rem;
  height: 2rem;
  display: flex;
  align-items: center;
}

.form-floating__thumb {
  display: block;
}

.form-floating p {
  margin: 0;
  width: 8rem;
  font-weight: 750;
}

.form-floating input {
  background-color: unset;
  border: unset !important;
  padding: 0 !important;
}

.form-floating input:focus-visible {
  border: none !important;
  box-shadow: none !important;
  outline: none;
}

.modal-body {
  padding: 0rem !important;
}

.modal-body hr {
  width: 100%;
}

.post__item {
  flex-grow: 1;
  flex-basis: 100%;
  display: flex;
  align-items: center;
  border-radius: 0.5rem;
  height: 3rem;
  position: relative;
  padding: 0 1rem;
  margin-left: 1rem;
  cursor: pointer;
}

.post__item:hover {
  background-color: var(--hover-color);
}

.media-preview {
  position: relative;
  width: 100%;
  display: flex;
  align-items: center;
}

.media-preview img {
  width: 100% !important;
  height: unset !important;
  border-radius: unset !important;
}

/* --------------- */

.conver__user {
  cursor: pointer;
}

.conver__user:hover {
  background-color: var(--hover-color);
}

.conver__user.active {
  background-color: var(--extra-bg);
  /* border-radius: .1rem; */
}
.conver__user img,
.modal img {
  width: 2.4rem;
  height: 2.4rem;
  border-radius: 50%;
  margin-right: 0.8rem;
}
</style>
