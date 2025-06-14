<template>
  <div class="row ms-1 me-1">
    <div class="form-wrapper">
      <form method="POST">
        <router-view @changeTypePassword="changeTypePassword"></router-view>
      </form>
    </div>
  </div>
</template>

<script setup>
// import { ref } from "vue";
// const title = ref("Đăng nhập");
function changeTypePassword(event) {
  const input = event.target.parentElement.querySelector('input')
  if (input.type == 'password') {
    input.type = 'text'
    event.target.classList.replace('fa-eye', 'fa-eye-slash')
  } else {
    input.type = 'password'
    event.target.classList.replace('fa-eye-slash', 'fa-eye')
  }
}
</script>

<style >
.form-wrapper {
  margin: auto;
  width: 35%;
  margin-top: 3rem;
  padding: 3.5rem;
  background-color: var(--main-extra-bg);
  border-radius: 1rem;
  box-shadow: 0 0.2rem 0.5rem 0 rgba(51, 62, 73, 0.1);
  position: relative;
}

.form-control {
  background-color: var(--main-extra-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 0.6rem !important;
  color: var(--font-color) !important;
}

.form-control:focus {
  box-shadow: 0 0 0 0.05rem var(--main1-color) !important;
  background-color: var(--main-extra-bg) !important;
  color: var(--font-color) !important;
}

.is-invalid {
  border: 1px solid #dc3545 !important;
  display: block !important;
}

.is-invalid:focus {
  border-color: #dc3545 !important;
  box-shadow: none !important;
}

.label-input {
  color: var(--font-input-color) !important;
}

.form-control .pwd-mg {
  position: relative;
}

.pwd-eye {
  position: absolute;
  right: 1rem;
  top: 1.5rem;
  color: var(--font-input-color);
}

.pwd-eye:hover {
  cursor: pointer;
}

#reload-capcha {
  font-size: 1.4rem;
}

#reload-capcha:hover {
  cursor: pointer;
}

.form-wrapper .mg-btn {
  display: block;
  width: 100%;
  background-color: var(--main1-color) !important;
  border-color: var(--main1-color) !important;
  box-shadow: 0 0 0 0.05rem var(--main1color) !important;
  border-radius: 0.6rem !important;
  margin: 1.2rem 0;
  color: var(--font-color);
  font-weight: 650;
  transition: 0.2s;
  height: 3rem;
}

.form-wrapper .mg-btn:focus {
  box-shadow: 0 0 0 0.25rem rgba(213, 214, 215, 0) !important;
  color: var(--font-color);
}

.form-wrapper .mg-btn:hover {
  transform: scale(1.02);
  color: var(--font-color);
  font-weight: 650;
}

.form-wrapper .form-check-input {
  background-color: var(--font-input-color) !important;
}

.form-wrapper .form-check-input:checked {
  background-color: var(--main-color) !important;
  border-color: var(--main-color) !important;
}

.form-wrapper .footer-form {
  position: absolute;
  bottom: -5rem;
  left: 50%;
  transform: translate(-50%);
  font-size: 1rem;
}

.form-wrapper .footer-form a {
  text-decoration: none;
  color: var(--font-color);
}

.form-wrapper a:hover {
  color: var(--main1-color) !important;
}

.form-wrapper i.back {
  position: absolute;
  top: 1.5rem;
  left: 2rem;
  font-size: 1.2rem;
}

.form-wrapper i.back:hover {
  cursor: pointer;
}

.form-wrapper a {
  text-decoration: none;
  color: var(--main1-color);
}

.form-wrapper .form-check-input {
  background-color: var(--font-input-color) !important;
}

.form-wrapper .form-check-input:checked {
  background-color: var(--main1-color) !important;
  border-color: var(--main1-color) !important;
  color: #000 !important;
}

.form-wrapper .form-check-input:checked[type='checkbox'] {
  --bs-form-check-bg-image: unset !important
    ;
}
</style>

