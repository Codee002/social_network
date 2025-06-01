<template>

  <div class="friendList__main row">
    <!-- FLASH MESSAGES -->

    <!-- Danh sách bạn bè -->
    <div class="col-12 col-lg-6 mb-3">
      <div class="friendList__main__info">
        <a href="/profile/14" class="d-flex">
          <img :src="srcAvtUser" class="friendList__main__info__avt" alt="" />
          <div class="friendList__main__info__name d-flex align-items-center">
            <p>Nguyễn Văn C</p>
          </div>
        </a>
        <i
          class="fa-solid fa-caret-down fs-4 friendList__main__info__menu__activeMenu"
          data-bs-toggle="dropdown"
        ></i>
        <ul
          class="dropdown-menu friendList__main__info__dropdownMenu"
          data-bs-theme="dark"
        >
          <li>
            <a
              class="dropdown-item"
              href="/profile/14"
              style="color: var(--font-color)"
              >Xem trang cá nhân</a
            >
          </li>
          <li>
            <hr class="dropdown-divider" />
          </li>
          <li>
            <a
              class="dropdown-item text-danger"
              href="#"
              data-bs-toggle="modal"
              data-bs-target="#modal1"
              ><strong>Hủy kết bạn</strong></a
            >
          </li>
        </ul>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="modal1">
      <div class="modal-dialog">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Xóa kết bạn</h4>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            Bạn có chắc chắn xóa kết bạn với <strong>Nguyễn Văn C</strong> ?
          </div>

          <!-- Modal footer -->
          <form action="/friend/removeFriend" method="POST">
            <input type="hidden" name="friend_id" value="14" />
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
              >
                Đóng
              </button>
              <button
                type="submit"
                class="btn btn-danger"
                data-bs-dismiss="modal"
              >
                Xóa
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6 mb-3">
      <div class="friendList__main__info">
        <a href="/profile/15" class="d-flex">
          <img :src="srcAvtUser" class="friendList__main__info__avt" alt="" />
          <div class="friendList__main__info__name d-flex align-items-center">
            <p>Lê Ngọc Đức</p>
          </div>
        </a>
        <i
          class="fa-solid fa-caret-down fs-4 friendList__main__info__menu__activeMenu"
          data-bs-toggle="dropdown"
        ></i>
        <ul
          class="dropdown-menu friendList__main__info__dropdownMenu"
          data-bs-theme="dark"
        >
          <li>
            <a
              class="dropdown-item"
              href="/profile/15"
              style="color: var(--font-color)"
              >Xem trang cá nhân</a
            >
          </li>
          <li>
            <hr class="dropdown-divider" />
          </li>
          <li>
            <a
              class="dropdown-item text-danger"
              href="#"
              data-bs-toggle="modal"
              data-bs-target="#modal2"
              ><strong>Hủy kết bạn</strong></a
            >
          </li>
        </ul>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="modal2">
      <div class="modal-dialog">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Xóa kết bạn</h4>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            Bạn có chắc chắn xóa kết bạn với <strong>Lê Ngọc Đức</strong> ?
          </div>

          <!-- Modal footer -->
          <form action="/friend/removeFriend" method="POST">
            <!-- Fix CSRF -->

            <input type="hidden" name="friend_id" value="15" />
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
              >
                Đóng
              </button>
              <button
                type="submit"
                class="btn btn-danger"
                data-bs-dismiss="modal"
              >
                Xóa
              </button>
            </div>
          </form>
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
.friendList__main__info {
  position: relative;
  height: 100%;
  padding: 1rem;
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  border-radius: 1rem;
  margin-bottom: 1rem;
  box-shadow: rgba(0, 0, 0, 0.1) 0.05rem 0.05rem;
}

.friendList__main__info:hover {
  background-color: var(--hover-color);
}

.friendList__main__info__avt {
  width: 5rem;
  border-radius: 1rem;
}

.friendList__main__info__name {
  width: 80%;
  padding: 0 1.2rem;
  font-weight: 550;
  font-size: 1.1rem;
}

.friendList__main__info__menu__activeMenu {
  position: absolute;
  right: 2rem;
  top: 2.5rem;
}

.friendList__main__info__dropdownMenu {
  background-color: var(--main-extra-bg) !important;
  box-shadow: rgba(0, 0, 0, 0.1) 0.05rem 0.05rem;
}
</style>
