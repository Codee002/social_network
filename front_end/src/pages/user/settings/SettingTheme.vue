<template>
  <div class="settingUserInfo settingTheme col-10 col-sm-8 offset-sm-1 offset-lg-1 col-lg-7 offset-xl-1 col-xl-7">
    <p class="settingUserInfo__title">Giao diện</p>
    <div class="settingTheme__container col-12 col-lg-10 offset-lg-1" style="position: relative">
      <p class="settingUserInfo__navWrapper__nav__title settingSecurity_navWrapper__nav__title">Giao diện tối</p>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" :checked="theme == 'dark'" @change="toggleTheme" />
      </div>
    </div>
  </div>
</template>

<script setup>
import Cookies from 'js-cookie'
import { ref } from 'vue'
const theme = ref()
theme.value = Cookies.get('theme') || 'dark'

function toggleTheme()
{
  theme.value = theme.value == "dark" ? "light" : "dark";
  Cookies.set("theme", theme.value)
  document.documentElement.setAttribute('data-bs-theme', theme.value)
}
</script>

<style scoped>
.settingUserInfo {
  margin-top: 2rem;
  margin-bottom: 5rem;
  background-color: var(--main-extra-bg);
  border-radius: 1rem;
  padding: 2rem 3rem;
  /* min-height: 100vh; */
  box-shadow: rgba(0, 0, 0, 0.1) 0.1rem 0.1rem;
}

.settingUserInfo__title {
  font-size: 1.4rem;
  font-weight: 600;
  margin-bottom: 2rem;
}

.settingTheme__container {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.settingSecurity_navWrapper__nav__title {
  margin-left: 1rem;
  font-size: 1rem;
  font-weight: 550;
  margin-bottom: 0.1rem;
}

.form-switch .form-check-input {
  width: 3rem;
  height: 1.5rem;
}

.form-check-input:checked {
  background-color: var(--main1-color);
  border-color: var(--main1-color);
}
</style>
