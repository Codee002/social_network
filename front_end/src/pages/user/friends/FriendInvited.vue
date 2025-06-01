<template>
  <div class="friendList__main row">
    <!-- FLASH MESSAGES -->

    <!-- Danh sách bạn bè -->
    <div class="col-12 col-lg-6 col-xl-4 col-xxl-3 mb-4">
      <div class="friendInvite__main__info">
        <a href="/profile/12">
          <img :src="srcAvtUser" class="friendInvite__main__info__avt" alt="" />
        </a>
        <div class="friendInvite__main__info__name">
          <a href="/profile/12">
            <p>Nguyễn Văn A</p>
          </a>
          <div class="friendInvite__main__info__option">
            <a href="/profile/12"> </a
            ><a
              href="/friend/accept/12"
              class="btn mb-2 friendInvite__main__info__option--accept"
              >Chấp nhận</a
            >
            <a
              href="/friend/ignore/12"
              class="btn btn-secondary friendInvite__main__info__option--ignore"
              >Từ chối</a
            >
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-6 col-xl-4 col-xxl-3 mb-4">
      <div class="friendInvite__main__info">
        <a href="/profile/13">
          <img :src="srcAvtUser" class="friendInvite__main__info__avt" alt="" />
        </a>
        <div class="friendInvite__main__info__name">
          <a href="/profile/13">
            <p>Trương Thị B</p>
          </a>
          <div class="friendInvite__main__info__option">
            <a href="/profile/13"> </a
            ><a
              href="/friend/accept/13"
              class="btn mb-2 friendInvite__main__info__option--accept"
              >Chấp nhận</a
            >
            <a
              href="/friend/ignore/13"
              class="btn btn-secondary friendInvite__main__info__option--ignore"
              >Từ chối</a
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";
defineProps({
  srcAvtUser: {
    type: String,
  },
});
</script>

<style scoped>
.friendInvite__main__info {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 90%;
  border: 1px solid var(--extra-bg);
  border-radius: 0.8rem;
  box-shadow: 0.01rem 0.1rem 0.3rem 0.015rem rgba(0, 0, 0, 0.15);
}

.friendInvite__main__info:hover {
  background-color: var(--hover-color);
}

.friendInvite__main__info__avt {
  max-width: 100%;
  border-top-left-radius: 0.8rem;
  border-top-right-radius: 0.8rem;
}

.friendInvite__main__info__name {
  display: flex;
  flex-direction: column;
  justify-content: start;
  font-weight: 500;
  padding: 0.8rem;
  font-size: 1rem;
  width: 100%;
}

.friendInvite__main__info__option {
  display: flex;
  flex-direction: column;
  margin-top: -1rem;
}

.friendInvite__main__info__option--accept {
  background-color: var(--main1-color) !important;
  color: #fff;
}

.friendInvite__main__info__option a {
  display: block;
  align-self: center;
  width: 100%;
  flex-wrap: nowrap;
  font-weight: 500;
  font-size: 1rem;
  padding: 0.4rem;
}
</style>
