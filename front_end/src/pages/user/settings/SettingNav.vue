<template>
  <div class="sidebar">
    <!-- NAV -->
    <div class="conver__action">
      <div class="conver__action__header">
        <h5 class="conver__header__title">Cài đặt</h5>
      </div>

      <div class="cover__action__search">
        <input type="text" class="form-control" placeholder="Tìm kiếm..." />
      </div>
    </div>

    <hr />

    <!-- Các lựa chọn -->
    <router-link :to="{ name: 'setting.info' }" >
      <div class="conver__user" :class="{ active: $route.name === 'setting.info' }">
        <nav-component :isHoverable="false">
          <template v-slot:icon>
            <i class="fa-solid fa-user"></i>
          </template>
          <template v-slot:des>Thông tin cá nhân</template>
        </nav-component>
      </div>
    </router-link>

    <router-link :to="{ name: 'setting.security' }" >
      <div class="conver__user" :class="{ active: $route.name === 'setting.security' }">
        <nav-component :isHoverable="false">
          <template v-slot:icon>
            <i class="fa-solid fa-gear"></i>
          </template>
          <template v-slot:des>Bảo mật</template>
        </nav-component>
      </div>
    </router-link>

    <router-link :to="{ name: 'setting.theme' }" >
      <div class="conver__user" :class="{ active: $route.name === 'setting.theme' }">
        <nav-component :isHoverable="false">
          <template v-slot:icon>
            <i class="fa-solid fa-moon"></i>
          </template>
          <template v-slot:des>Giao diện</template>
        </nav-component>
      </div>
    </router-link>
  </div>
</template>

<script setup>
import NavComponent from '@/layouts/partials/NavComponent.vue'


</script>

<style scoped>
.sidebar {
  width: 20rem;
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

.cover__action__search input {
  background-color: var(--main-extra-bg) !important;
  border: unset !important;
  height: 3rem;
}

.conver__user {
  cursor: pointer;
  display: flex;
  height: 5rem;
  align-items: center;
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
