<template>
  <div class="profile__cover">
    <img alt="" />
    <button
      aria-label="add-cover"
      data-bs-toggle="modal"
      data-bs-target="#modal__upload--cover"
    >
      <i class="fa-solid fa-camera"></i>
      <p class="d-none d-md-flex">Thêm ảnh bìa</p>
    </button>

    <div
      class="modal modal__upload modal__upload--cover"
      id="modal__upload--cover"
      tabindex="-1"
    >
      <div class="modal-dialog modal-dialog-centered">
        <form
          class="modal-content"
          action="/profile/cover"
          method="post"
          enctype="multipart/form-data"
        >
          <div class="modal-header">
            <h5 class="modal-title text-center" id="modal-label">Ảnh bìa</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body">
            <div class="upload__preview--cover">
              <img :src="srcThumb" alt="" />
              <input
                class="upload__cover form-control"
                type="file"
                name="cover"
                accept="image/*"
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
              type="submit"
              class="upload__btn btn upload__btn--primary upload__save hide"
            >
              Cập nhật
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";

defineProps({
  srcThumb: {
    type: String,
    require: true,
  },
});
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

.modal-content {
  background-color: var(--main-extra-bg) !important;
}

.modal-header {
  top: 0;
  width: 100%;
  justify-content: center;
  background-color: var(--main-extra-bg);
}

.modal-close {
  position: absolute;
  padding: 0;
  left: 1rem;
  color: var(--font-color);
}

.upload__btn--secondary {
  background-color: var(--extra-bg);
  color: var(--font-color);
}

.upload__btn--primary {
  background-color: var(--main1-color);
  color: #fff;
}
</style>
