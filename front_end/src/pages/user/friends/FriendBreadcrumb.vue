<template>
  <div class="friendList__header row">
    <div class="friendList__header__title col-4 col-lg-3">{{ show.title }}</div>
    <div
      v-if="show.form == true"
      class="friendList__header__search col-4 col-lg-6"
    >
      <form action="/friend/list" method="POST">
        <div class="input-group">
          <span class="input-group-text"
            ><i class="fa-solid fa-magnifying-glass"></i
          ></span>
          <input
            class="friendList__header__search-input form-control"
            name="search"
            placeholder="Tìm kiếm..."
            value=""
          />
        </div>
      </form>
    </div>

    <div
      class="friendList__header__invite col-4 col-lg-3"
      :class="{ 'offset-lg-6': !show.form }"
    >
      <a @click="changeMode(show.mode)"> {{ show.navName }} </a>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";
defineProps({
  show: {},
});

const emit = defineEmits(["changeMode"]);

function changeMode(change) {
  emit("changeMode", change);
}
</script>

<style scoped>
.friendList__header {
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  margin-bottom: 2rem;
}

.friendList__header__title {
  justify-content: start;
  font-size: 1.4rem;
  font-weight: 650;
  /* margin-left: 2rem; */
}

.friendList__header__search {
  display: flex;
  align-items: center;
  justify-content: center;
}

.friendList__header__search form {
  margin: 0;
  display: flex;
  justify-content: center;
  width: 100%;
}

.friendList__header__search .input-group {
  width: 75%;
}

.friendList__header__search-input {
  background-color: var(--main-extra-bg);
  padding: 0.6rem;
  border-radius: 0rem !important;
  border-top-right-radius: 0.6rem !important;
  border-bottom-right-radius: 0.6rem !important;
  border: 1px solid var(--border-color) !important;
}

.friendList__header__search .input-group-text {
  background-color: var(--main-extra-bg) !important;
}

.friendList__header__invite {
  display: flex;
  align-items: center;
  text-align: center;
  justify-content: end;
  font-size: 0.9rem;
  font-weight: 550;
}

.friendList__header__invite a {
  color: #75b6ff;
  cursor: pointer;
}

.friendList__header__invite a:hover {
  color: #75b6ff;
}
</style>
