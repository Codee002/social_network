<template>
  <div class="profile__story__container col-12 col-sm-8 offset-sm-2">
    <div class="main-mid__story">
      <div class="story-container" ref="storyContainer">
        <div class="story-card">
          <img
            class="story__img"
            :src="
              owner.profile.avatar
                ? $backendBaseUrl + owner.profile.avatar
                : require('@/assets/images/avatar/default.jpg')
            "
            alt=""
          />
          <div class="story__add icon-box" data-bs-toggle="modal" data-bs-target="#modal__upload--story">
            <i class="fa-solid fa-plus"></i>
          </div>
          <div class="story__txt story__txt--new">Tạo tin</div>
        </div>
        <!-- Modal -->
        <div class="modal modal__upload modal__upload--cover" id="modal__upload--story" tabindex="-1" ref="thumbModal">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-center" id="modal-label">Tạo tin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="upload__preview--avt d-flex flex-column align-items-center">
                  <div v-if="mediaFiles.length == 0">
                    <p>Chọn phương tiện để đăng tin</p>
                  </div>
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
                  <div class="post__option">
                    <div class="post__item post__item--image" @click="triggerFileInput">
                      <i class="fa-solid fa-images text-success me-2"></i>
                      <span>Hình ảnh</span>
                    </div>
                    <div class="post__item post__item--image" @click="triggerFileInput">
                      <i class="fa-brands fa-youtube text-danger me-2"></i>
                      <span>Video</span>
                    </div>
                  </div>
                  <input
                    class="upload__avt form-control d-none"
                    @change="handleFilesChange"
                    type="file"
                    accept="image/*,video/*"
                    ref="fileInput"
                  />
                </div>
              </div>
              <div class="modal-footer">
                <button
                  data-bs-dismiss="modal"
                  type="button"
                  class="upload__btn btn upload__btn--secondary upload__reset"
                >
                  Hủy
                </button>
                <button
                  class="upload__btn btn upload__btn--primary upload__save hide"
                  @click="createStory"
                  :disabled="mediaFiles.length == 0"
                >
                  Tạo tin
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Story -->
        <div v-for="story in stories" :key="story.id">
          <router-link :to="{ name: 'story', params: { story_id: story.id } }">
            <div class="story-card">
              <img
                class="story__img"
                :src="$backendBaseUrl + story.media.path"
                alt=""
                v-if="story.media.type == 'image'"
              />
              <div class="story__avt" :class="{ 'story__avt--new': !views[story.id].includes(owner.id) }">
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
              <div class="story__txt">{{ story.user.profile.name }}</div>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { defineProps, onMounted, ref } from 'vue'
import { useToast } from 'vue-toastification'
defineProps({
  owner: {},
})
const storyContainer = ref()
const stories = ref()
const views = ref()

onMounted(async () => {
  try {
    let res = await axios.get(`/getStories`)
    stories.value = res.data.stories
    views.value = res.data.views
  } catch (error) {
    console.log('Không lấy được thông tin!', error)
  }
})

// ---------------------Đăng Story---------------------
const thumbModal = ref()
const mediaFiles = ref([])
const fileInput = ref([])
const toast = useToast()
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

async function createStory() {
  if (mediaFiles.value.length == 0) return
  try {
    let formData = new FormData()
    formData.append('media', mediaFiles.value[0].file)
    console.log(mediaFiles.value[0].file)
    await axios.post(`story/createStory`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    window.bootstrap.Modal.getInstance(thumbModal.value).hide()
    toast.success('Đăng tin thành công', {
      position: 'bottom-right',
    })
  } catch (error) {
    console.log('Đăng tin thất bại!', error)
    toast.error('Có lỗi khi đăng tin', {
      position: 'bottom-right',
    })
  }
}
</script>

<style scoped>
.profile__story__container {
  border: 1px solid var(--extra-bg);
  border-radius: 0.5rem;
  background-color: var(--main-extra-bg);
  padding: 2rem 3rem;
  margin-top: 2rem;
}

.main-mid__story {
  position: relative;
  display: flex;
}

.story-container {
  display: flex;
  gap: 0.5rem;
  background-color: rgba(0, 0, 0, 0);
  padding-block: 0.5rem;
  flex-wrap: wrap;
}

.story-card {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  height: 13rem;
  min-width: 7.6rem;
  width: 7.6rem;
  background-color: var(--main-extra-bg);
  box-shadow: rgba(0, 0, 0, 0.2) 0.2rem 0.2rem 0.2rem;
  border-radius: 0.75rem;
  overflow: hidden;
}

.story__img:has(+ .story__add) {
  height: 75%;
  transform: scale(1.1);
}

.story__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0.8;
}

.story__add {
  position: absolute;
  cursor: pointer;
  height: 22%;
  aspect-ratio: 1 / 1;
  border-radius: 100%;
  border: 0.3rem solid var(--main-extra-bg);
  background-color: var(--main1-color);
  box-shadow: rgba(0, 0, 0, 0.1) 0px -5px 5px 0px;
  bottom: 13.5%;
  z-index: 1;
  color: #fff;
  font-size: 1.5rem;
  -webkit-text-stroke: 0.05rem;
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

.story__avt--new {
  border: 0.3rem solid var(--main1-color);
}

.icon-box {
  display: flex;
  aspect-ratio: 1 / 1;
  justify-content: center;
  align-items: center;
}

.story__add + .story__txt {
  background-color: var(--main-extra-bg);
  text-align: center;
  padding-left: 0;
  box-shadow: rgba(0, 0, 0, 0.1) 0px -2px 8px 0px;
}

.story__txt--new {
  color: var(--font-color);
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

.upload__btn--secondary {
  background-color: var(--extra-bg);
  color: var(--font-color);
}

.upload__btn--primary {
  background-color: var(--main1-color);
  color: #fff;
}

.post__option {
  display: flex;
  align-items: center;
  justify-content: space-around;
  width: 100%;
  cursor: pointer;
  margin: 0;
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
