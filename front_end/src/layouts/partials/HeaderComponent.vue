<template>
  <header>
    <div
      class="d-flex align-items-center"
      style="height: 100%; width: 100em; margin-right: 4rem"
    >
      <img class="logo" :src="srcLogoImage" />
      <form action="" class="d-flex align-items-center" style="height: 100%">
        <search-component
          class="search"
          :name="field"
          :placeholder="placeholder"
        ></search-component>
      </form>
    </div>

    <div
      class="header-nav d-flex align-items-center"
      style="height: 100%; width: 100em"
    >
      <router-link :to="{ name: 'home' }">
        <icon-nav :isActive="{ 'is-active': $route.name === 'home' }"
          ><i class="fa-solid fa-house"></i
        ></icon-nav>
      </router-link>
      <router-link :to="{ name: 'friend' }">
        <icon-nav :isActive="{ 'is-active': $route.name === 'friend' }"
          ><i class="fa-solid fa-user-group"></i></icon-nav
      ></router-link>
      <router-link :to="{ name: 'profile' }">
        <icon-nav :isActive="{ 'is-active': $route.name === 'profile' }"
          ><i class="fa-solid fa-user"></i></icon-nav
      ></router-link>
      <router-link :to="{ name: 'favorite' }">
        <icon-nav :isActive="{ 'is-active': $route.name === 'favorite' }"
          ><i class="fa-solid fa-heart"></i></icon-nav
      ></router-link>
      <router-link :to="{ name: 'conversation' }">
        <icon-nav :isActive="{ 'is-active': $route.name === 'conversation' }"
          ><i class="fa-solid fa-message"></i></icon-nav
      ></router-link>
    </div>

    <div
      class="info-group dropdown-toggle"
      data-bs-toggle="dropdown"
      type="button"
      style="height: 100%; width: 100em"
    >
      <img class="avatar" :src="srcAvtUser" alt="" />

      <ul class="dropdown-menu">
        <router-link :to="{ name: 'profile' }" class="d-flex">
          <nav-component>
            <template v-slot:icon>
              <img class="avatar" :src="srcAvtUser" alt="" />
            </template>
            <template v-slot:des> Trang cá nhân </template>
          </nav-component></router-link
        >
        <hr />

        <router-link class="d-flex">
          <nav-component>
            <template v-slot:icon>
              <i class="fa-solid fa-gear"></i>
            </template>
            <template v-slot:des> Cài đặt & quyền riêng tư </template>
          </nav-component></router-link
        >
        <router-link class="d-flex"
          ><nav-component>
            <template v-slot:icon>
              <i class="fa-solid fa-right-from-bracket"></i>
            </template>
            <template v-slot:des> Đăng xuất </template>
          </nav-component></router-link
        >
      </ul>
    </div>
  </header>
</template>

<script setup>
// ----------------------- Import -----------------------
import { ref } from "vue";
import SearchComponent from "./SearchComponent.vue";
import IconNav from "./IconNav.vue";
import NavComponent from "./NavComponent.vue";
// ----------------------- Var -----------------------
const field = ref("search");
const placeholder = ref("Tìm kiếm");
const srcLogoImage = ref(require("@/assets/images/general/logo.png"));
const srcAvtUser = ref(require("@/assets/images/avatar/default.jpg"));

// ----------------------- Function -----------------------
</script>

<style scoped>
header {
  position: fixed;
  display: flex;
  top: 0;
  height: 3.6rem;
  border-color: var(--main1-color) !important;
  box-shadow: 0 0 0 0.05rem var(--main1color) !important;
  background-color: var(--main-extra-bg);
  width: 100%;
  justify-content: space-between;
  align-items: center;
  z-index: 100;
  border-bottom: 0.01rem solid var(--border-color) !important;
}

header .logo {
  height: 70%;
  margin: 0 0.8rem;
}

header .avatar {
  width: 10%;
  border-radius: 2rem;
  cursor: pointer;
}

.header-nav {
  display: flex;
  width: 40rem;
  justify-content: space-evenly;
  align-items: center;
  gap: 1rem;
}

.info-group {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: end;
  margin: 1rem;
}

.dropdown-menu {
  background-color: var(--main-extra-bg);
  width: 22rem;
  top: 3.4rem !important;
  transform: none !important;
  border: none;
  border-radius: 0 0 0.5rem 0.5rem;
  box-shadow: rgba(0, 0, 0, 0.1) -0.1rem 0.5rem 0.5rem 0.25rem;
  left: 6rem !important;
  padding: 0.6rem;
}

.dropdown-toggle::after {
  content: unset !important;
}
</style>
