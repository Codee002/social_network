<template>
  <div class="side__bar">
    <div class="side__bar__title">
      <p>Quản lý</p>
    </div>
    <hr />
    <div class="side__bar__feature">
      <div class="feature__container">
        <p class="feature__title">Tài khoản</p>
        <div class="feature__group">
          <router-link :to="{ name: 'admin.account' }" class="d-flex" style="width: 100%">
            <div class="d-flex nav-group">
              <i class="fa-solid fa-user"></i>
              Danh sách tài khoản
              <br />
            </div>
          </router-link>

          <router-link :to="{ name: 'admin.account.report' }" class="d-flex" style="width: 100%">
            <div class="d-flex nav-group">
              <i class="fa-solid fa-flag"></i>
              Danh sách tố cáo
              <br />
            </div>
          </router-link>
        </div>
      </div>

      <div class="feature__container">
        <p class="feature__title">Bài viết</p>
        <div class="feature__group">
          <router-link :to="{ name: 'admin.post' }" class="d-flex" style="width: 100%">
            <div class="d-flex nav-group">
              <i class="fa-solid fa-clipboard-list"></i>
              Danh sách bài viết
              <br />
            </div>
          </router-link>

          <router-link :to="{ name: 'admin.post.report' }" class="d-flex" style="width: 100%">
            <div class="d-flex nav-group">
              <i class="fa-solid fa-flag"></i>
              Danh sách tố cáo
              <br />
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.side__bar {
  display: flex;
  flex-direction: column;
  background-color: var(--main-extra-bg);
  border: 1px solid var(--extra-bg);
  min-height: 100%;
  position: fixed;
  width: 18%;
}

.side__bar__title {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  margin: 1rem 0rem;
}

.side__bar__title p {
  padding: 0;
  margin: 0;
  font-weight: 600;
  font-size: 1.4rem;
}

.side__bar__feature {
  display: flex;
  flex-direction: column;
  width: 100%;
  /* font-weight: 500; */
}

.feature__container {
  display: flex;
  width: 100%;
  flex-direction: column;
  padding: 0.4rem 1rem;
}

.feature__title {
  font-size: 1rem;
  padding: 0rem;
  margin: 0;
}

.nav-group {
  width: 100%;
  height: 100%;
  justify-content: start;
  align-items: center;
  padding: 0.8rem;
  border-radius: 0.8rem;
  /* font-weight: 500; */
  font-size: 1rem;
  margin-left: 0.2rem;
}

.nav-group i {
  font-size: 1rem;
  margin-right: 0.8rem;
}

.nav-group:hover {
  background-color: var(--extra-bg);
}
</style>
