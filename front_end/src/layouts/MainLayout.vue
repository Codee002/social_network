<template>
  <div v-if="owner" style="height: 100%">
    <header-component :user="owner"></header-component>
    <div style="padding-top: 3.5rem"></div>
    <router-view :owner='owner'></router-view>
  </div>
  <div class="spinner-border" v-else style="margin: auto"></div>
</template>

<script setup>
// import axios from 'axios'
import { onMounted, ref } from 'vue'
import HeaderComponent from './partials/HeaderComponent.vue'
import auth from '@/utils/auth'

const owner = ref(null)

// Lấy thông tin chủ tài khoản
onMounted(async () => {
  try {
    owner.value = await auth.getOwner()
  } catch (error) {
    console.log('Không lấy được thông tin User!', error)
    owner.value = null
  }
})
</script>


<style scoped>
.spinner-border {
  position: fixed;
  top: 50%;
  left: 50%;
}
</style>
