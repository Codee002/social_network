<template>
  <!-- Đăng bài -->
  <div class="post-card">
    <div class="post__header">
      <div class="post__author">
        <img
          class="post__avt rounded-circle"
          :src="
            user.profile.avatar ? $backendBaseUrl + user.profile.avatar : require('@/assets/images/avatar/default.jpg')
          "
          alt=""
        />
        <!-- Hiển thị tiến trình -->
        <div v-if="uploadProgress > 0 && uploadProgress < 100" class="upload-bar">
          <div class="progress" :style="{ width: uploadProgress + '%' }"></div>
          <span>Đang upload: {{ uploadProgress }}%</span>
        </div>
        <div v-else class="post__upload" data-bs-toggle="modal" data-bs-target="#modal__upload--post">
          <p>Xin chào, bạn đang nghĩ gì thế?</p>
        </div>
      </div>
    </div>
    <div class="post__footer" v-if="uploadProgress == 0">
      <div class="post__option">
        <div class="post__item post__item--text" data-bs-toggle="modal" data-bs-target="#modal__upload--post">
          <i class="fa-solid fa-book text-primary me-2"></i>
          <span>Bài viết</span>
        </div>
        <div class="post__item post__item--image" data-bs-toggle="modal" data-bs-target="#modal__upload--post">
          <i class="fa-solid fa-images text-success me-2"></i>
          <span>Hình ảnh</span>
        </div>
        <div class="post__item post__item--video" data-bs-toggle="modal" data-bs-target="#modal__upload--post">
          <i class="fa-brands fa-youtube text-danger me-2"></i>
          <span>Video</span>
        </div>
      </div>
    </div>

    <div
      class="modal modal__upload modal__upload--post"
      id="modal__upload--post"
      tabindex="-1"
      aria-labelledby="modal-label"
      style="display: none"
      aria-hidden="true"
      ref="postModal"
    >
      <div class="modal-dialog modal-dialog-centered">
        <form @submit.prevent="onSubmit" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal-label">Tạo bài viết</h5>
            <button data-v-82489926="" type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="upload__author">
              <div class="post__header">
                <div class="post__author">
                  <a>
                    <img
                      class="post__avt rounded-circle"
                      :src="
                        user.profile.avatar
                          ? $backendBaseUrl + user.profile.avatar
                          : require('@/assets/images/avatar/default.jpg')
                      "
                      alt=""
                    />
                  </a>
                  <div class="post__detail">
                    <a class="post__name">{{ user.profile.name }}</a>
                    <div class="post__privacy">
                      <i class="fa-solid fa-earth-americas" :class="{ 'd-none': form.rule != 'public' }"></i>
                      <i class="fa-solid fa-user" :class="{ 'd-none': form.rule != 'friend' }"></i>
                      <i class="fa-solid fa-lock" :class="{ 'd-none': form.rule != 'private' }"></i>
                      <select name="post_privacy" v-model="form.rule">
                        <option value="private">Riêng tư</option>
                        <option value="public">Công khai</option>
                        <option value="friend">Bạn bè</option>
                      </select>
                    </div>
                  </div>
                </div>

                <input
                  type="file"
                  ref="fileInput"
                  multiple
                  accept="image/*,video/*"
                  @change="handleFilesChange"
                  style="display: none"
                />
              </div>
            </div>
            <div class="upload__content">
              <textarea
                name="post_text"
                v-model="form.caption"
                :rows="mediaFiles.length > 0 ? 1 : 4"
                :placeholder="mediaFiles.length == 0 ? 'Xin chào, bạn đang nghĩ gì thế?' : 'Mô tả cho bài viết'"
              ></textarea>
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
                <video v-else-if="file.type.startsWith('video/')" :src="file.url" controls />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="post__option">
              <div class="post__item post__item--image" @click="triggerFileInput">
                <i class="fa-solid fa-images text-success me-2"></i>
                <span>Hình ảnh</span>
              </div>
              <div class="post__item post__item--video" @click="triggerFileInput">
                <i class="fa-brands fa-youtube text-danger me-2"></i>
                <span>Video</span>
              </div>
            </div>
            <button type="submit" class="upload__btn upload__btn--primary">Đăng</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { reactive, ref, defineProps } from 'vue'
import { useToast } from 'vue-toastification'

defineProps({
  user: {},
})

const fileInput = ref()
const mediaFiles = ref([])
const defaultForm = reactive({
  caption: '',
  rule: 'private',
  type: '',
  status: 'actived',
  media: [],
})

const form = reactive({ ...defaultForm })

const postModal = ref()
const toast = useToast()
const errors = reactive({})
const uploadProgress = ref(0)

const triggerFileInput = () => {
  fileInput.value.click()
}

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

const removeFile = (index) => {
  mediaFiles.value.splice(index, 1)
}

const onSubmit = async () => {
  // Không trùng
  if (mediaFiles.value.length == 0 && form.caption.trim().length == 0) {
    errors.caption = 'Vui lòng nhập nội dung'
    toast.error(errors.caption, {
      position: 'top-center',
    })
    return
  }

  // Data submit
  const formData = new FormData()
  formData.append('caption', form.caption)
  formData.append('rule', form.rule)
  formData.append('status', form.status)

  if (mediaFiles.value.length != 0) {
    formData.append('type', 'media')
    mediaFiles.value.forEach((media) => {
      formData.append('media[]', media.file)
    })
  } else {
    formData.append('type', 'text')
  }

  window.bootstrap.Modal.getInstance(postModal.value).hide()
  try {
    await axios.post('/post/store', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      onUploadProgress: (e) => {
        const percent = Math.round((e.loaded * 100) / e.total)
        uploadProgress.value = percent
      },
    })
    uploadProgress.value = 0
    Object.assign(form, defaultForm)
    mediaFiles.value = []

    toast.success('Đăng bài thành công', {
      position: 'bottom-right',
    })
  } catch (err) {
    console.error('Lỗi khi đăng bài:', err)
  }
}
</script>

<style scoped>
.post__upload {
  flex-grow: 1;
  display: flex;
  align-items: center;
  border-radius: calc(var(--header-heigh) * 0.9);
  background-color: var(--extra-bg);
  cursor: pointer;
}

.post__upload:hover {
  filter: var(--brightness);
}

.post__upload p {
  margin-block: auto;
  margin-left: 1rem;
}

.upload__content textarea {
  width: 100%;
  background-color: transparent;
  padding: 0.4rem;
  font-size: 1.2rem;
  border: none;
}

.upload__content textarea:focus-visible {
  outline: none;
}

.post__option {
  display: flex;
  align-items: center;
  justify-content: space-around;
  width: 100%;
  cursor: pointer;
  margin: 0;
}

.post__option i {
  font-size: 1.4rem;
}

.post__item {
  flex-grow: 1;
  flex-basis: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 0.5rem;
  height: 3rem;
  position: relative;
}

.post__item:hover {
  background-color: var(--hover-color);
}

.media-preview {
  /* position: relative; */
  border-radius: 0.5rem;
  padding: 0.5rem;
  margin-top: 0.5rem;
  border: 0.1rem solid var(--extra-bg);
  overflow-y: auto;
  max-height: 100%;
  min-height: 100%;
  width: 100% !important;
}

.media-preview .media__deleteBtn {
  position: absolute;
  right: 1rem;
  top: 1rem;
  font-size: 1.5rem;
  cursor: pointer;
  z-index: 10;
}

.media-preview img,
.media-preview video {
  width: 100% !important;
  border-radius: 0.5rem !important;
  height: 100% !important;
  margin: 0 !important;
}

#modal__upload--post .post__privacy i {
  position: absolute;
  left: 0.35rem;
  top: 0.2rem;
}

/* Modal */
#modal__upload--post select {
  background-color: var(--extra-bg);
  border-radius: 0.25rem;
  padding-left: 1.25rem;
  color: var(--font-color);
  font-weight: 600;
  border: none;
}

#modal__upload--post .modal-body {
  margin-top: var(--header-heigh);
  padding: 0;
  overflow-y: auto;
  min-height: 100%;
  max-height: 150%;
}

#modal__upload--post .upload__btn {
  height: 2.5rem;
  width: 100%;
  border-radius: 0.5rem;
  font-weight: 600;
  margin: 0;
  border: none;
}

.upload-bar {
  position: relative;
  background: #eee;
  height: 10px;
  border-radius: 6px;
  margin-top: 10px;
  margin-bottom: 10px;
  width: 100%;
}
.progress {
  background: var(--main1-color);
  height: 100%;
  border-radius: 6px;
  transition: width 0.3s;
}
</style>
