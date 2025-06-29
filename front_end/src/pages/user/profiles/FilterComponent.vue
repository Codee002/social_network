<template>
  <div class="profile__card profile__card--post">
    <div class="profile__card__header">
      <div class="d-flex justify-content-between align-items-center">
        <div class="profile__card__heading">Bài viết</div>
        <div class="profile__card__option">
          <div class="profile__card__btn my-0 px-3">
            <i class="bi bi-sliders"></i>
            Bộ lọc
          </div>
          <div class="profile__card__btn my-0 px-3">
            <i class="bi bi-gear-fill"></i>
            Quản lý bài viết
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile__card {
  color: var(--font-color);
  background-color: var(--main-extra-bg);
  border-radius: 0.75rem;
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.1rem 0.1rem;
  padding-inline: 1rem;
  padding-block: 0.35rem;
}

.profile__card__header {
  padding-block: 0.65rem;
  font-size: 1.0625rem;
  font-weight: 400;
  line-height: 1.25rem;
}

.profile__card__heading {
  font-size: 1.25rem;
  font-weight: 700;
  line-height: 1.5rem;
}

.profile__card__option {
  display: flex;
  gap: 0.5rem;
}

.profile__card__btn {
  height: 2.25rem;
  align-content: center;
  text-align: center;
  border-radius: 0.3rem;
  background-color: var(--extra-bg);
  font-size: 0.9375rem;
  font-weight: 600;
  margin-bottom: 0.65rem;
}
</style>
