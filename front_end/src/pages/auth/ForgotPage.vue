<template>
  <router-link :to="{ name: 'auth.login' }">
    <i class="fa-solid fa-arrow-left back"></i>
  </router-link>

  <p class="text-center fs-3 fw-semibold fw-bolder">Quên mật khẩu?</p>

  <p class="text-center">Nhập vào tên đăng nhập của bạn để lấy lại mật khẩu</p>

  <!-- Username -->
  <form @submit.prevent="onSubmit()" method="POST">
    <div class="form-group mg-form">
      <div class="form-floating mb-3 mt-3">
        <input
          type="text"
          class="form-control"
          value=""
          id="username"
          placeholder="Enter username"
          name="username"
          autocomplete="off"
        />
        <label class="label-input" for="username">Tên đăng nhập</label>
      </div>
    </div>

    <!-- Capcha -->
    <div class="form-group mg-form mb-3" style="align-items: center">
      <div class="form-floating mt-3 mb-0 d-flex justify-content-start" style="align-items: center" id="capcha-group">
        <input type="text" class="form-control" placeholder="" name="captcha" />
        <label class="label-input" for="">Capcha</label>

        <img
          id="capcha-img"
          src="data:image/jpeg;base64,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"
          style="height: 100%"
          class="ms-3 me-3"
        />

        <i id="reload-capcha" onclick="reloadCapcha()" class="fa-solid fa-rotate-right"></i>
      </div>
    </div>

    <button type="button" @click="onSubmit()" class="btn btn-primary mg-btn">Tìm tài khoản</button>
  </form>
  <hr />
  <p class="text-center">
    Chưa có tài khoản?
    <a class="text-decoration-none" href="http://vibez.localhost/register">Đăng ký</a>
  </p>
</template>
