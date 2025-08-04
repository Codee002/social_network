<template>
  <div class="profile__cover">
    <img
      :src="user.profile.thumb ? $backendBaseUrl + user.profile.thumb : require('@/assets/images/thumb/default.png')"
      style="width: 100%; height: 100%; object-fit: cover"
    />
    <button
      v-if="relationStatus == 'owner'"
      aria-label="add-cover"
      data-bs-toggle="modal"
      data-bs-target="#modal__upload--cover"
    >
      <i class="fa-solid fa-camera"></i>
      <p class="d-none d-md-flex">Thêm ảnh bìa</p>
    </button>

    <div class="modal modal__upload modal__upload--cover" id="modal__upload--cover" tabindex="-1" ref='thumbModal'>
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-center" id="modal-label">Ảnh bìa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="upload__preview--avt d-flex flex-column align-items-center">
              <img
                v-if="mediaFiles.length == 0"
                class="cover"
                :src="
                  user.profile.thumb
                    ? $backendBaseUrl + user.profile.thumb
                    : require('@/assets/images/thumb/default.png')
                "
                alt=""
              />
              <div v-if="mediaFiles.length != 0" class="">
                <div
                  v-for="(file, index) in mediaFiles"
                  :key="index"
                  class="media-item media-preview"
                  style="width: unset"
                >
                  <i @click="removeFile(index)" class="fa-solid fa-xmark media__deleteBtn"></i>
                  <img :src="file.url" />
                </div>
              </div>
              <div class="post__item post__item--image" @click="triggerFileInput">
                <i class="fa-solid fa-images text-success me-2"></i>
                <span>Hình ảnh</span>
              </div>
              <input
                class="upload__avt form-control d-none"
                @change="handleFilesChange"
                type="file"
                accept="image/*"
                ref="fileInput"
              />
            </div>
          </div>
          <div class="modal-footer">
            <button data-bs-dismiss="modal" type="button" class="upload__btn btn upload__btn--secondary upload__reset">
              Hủy
            </button>
            <button class="upload__btn btn upload__btn--primary upload__save hide" :disabled="mediaFiles.length == 0" @click="storeThumb">Cập nhật</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { defineProps, ref } from 'vue'
import { useToast } from 'vue-toastification'
const props = defineProps({
  srcThumb: {
    type: String,
    require: true,
  },

  relationStatus: {
    type: String,
  },

  user: {
    type: Object,
    default: null,
  },
})

const user = ref({})
Object.assign(user.value, props.user)

const thumbModal = ref()
const mediaFiles = ref([])
const fileInput = ref([])
const toast = useToast()

// ---------------------Đổi Thumb ---------------------
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

const triggerFileInput = () => {
  fileInput.value.click()
}

const removeFile = (index) => {
  mediaFiles.value.splice(index, 1)
}

async function storeThumb() {
  if (mediaFiles.value.length == 0) return
  try {
    let formData = new FormData()
    formData.append('thumb', mediaFiles.value[0].file)
    console.log(mediaFiles.value[0].file)
    let res = await axios.post(`storeThumb`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    window.bootstrap.Modal.getInstance(thumbModal.value).hide()
    user.value.profile.thumb = res.data.thumb
    toast.success('Đổi ảnh bìa thành công', {
      position: 'bottom-right',
    })
  } catch (error) {
    console.log('Gửi tin nhắn thất bại!', error)
  }
}
</script>

<style scoped>
.profile__cover {
  position: relative;
  background-color: var(--main-bg);
  width: 100%;
  aspect-ratio: 256 / 95;
}

.profile__cover > button {
  position: absolute;
  bottom: 1rem;
  right: 2rem;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.25rem;
  height: 2.5rem;
  min-width: 2.5rem;
  color: #000;
  font-weight: 600;
  padding-inline: 0.6rem;
  border: none;
  border-radius: 0.6rem;
  background-color: #fff;
}

.profile__cover p {
  margin: auto;
}

.upload__btn--secondary {
  background-color: var(--extra-bg);
  color: var(--font-color);
}

.upload__btn--primary {
  background-color: var(--main1-color);
  color: #fff;
}

.post__item {
  display: flex;
  width: 50%;
  justify-content: center;
  align-items: center;
  border-radius: 0.5rem;
  height: 3rem;
  position: relative;
  cursor: pointer;
  margin-top: 1rem;
}

.post__item:hover {
  background-color: var(--hover-color);
}

img.cover {
  border-radius: 1rem;
  width: 100%;
}
</style>
