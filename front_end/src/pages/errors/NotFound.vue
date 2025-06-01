<template>
  <div class="not-found">
    <h1>404 - Page Not Found</h1>
    <p>Trang bạn tìm không tồn tại.</p>
    <router-link :to="{ name: 'home' }">Quay lại trang chủ</router-link>
  </div>
</template>

<style scoped>
.not-found {
  text-align: center;
  padding: 5rem;
  margin: 2rem;
}

.not-found h1 {
  font-size: 2rem;
  color: #ff4d4f;
}

.not-found p {
  font-size: 1.2rem;
  margin: 1rem 0;
}

.not-found a {
  color: var(--main1-color);
  text-decoration: none;
  font-weight: bold;
}

.not-found a:hover {
  text-decoration: underline;
}
</style>s
