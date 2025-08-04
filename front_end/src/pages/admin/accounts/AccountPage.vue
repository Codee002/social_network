<template>
  <main class="app-main p-0" style="height: 100%">
    <div class="container" style="width: 90%">
      <h2 class="text-center fw-bolder mt-4 mb-4">Danh sách tài khoản</h2>
      <div class="d-flex align-items-center mb-1 row">
        <div class="col-6">
          <div class="form-group d-flex">
            <input placeholder="Tên" v-model="nameSearch" class="form-control me-1" />
            <select name="rank" class="form-select me-1" v-model="statusSearch">
              <option value="" disabled="" selected="">Trạng thái</option>
              <option value="actived">Đang hoạt động</option>
              <option value="disabled">Đang khóa</option>
            </select>

            <button type="submit" class="btn btn-primary text-white text-decoration-none m-1">Tìm</button>
          </div>
        </div>
        <div class="text-end col-6">
          <!-- <a href="http://vibez.localhost/admin/account/export" class="btn btn-success text-white text-end ms-3">
            Xuất Excel
          </a> -->
        </div>
      </div>

      <table class="table table-bordered table-striped mt-3" v-if="accountSearch.length != 0">
        <thead>
          <tr>
            <th>Mã</th>
            <th>Tên</th>
            <th>Số điện thoại</th>
            <th>Email</th>
            <th>Trạng thái</th>
            <th>Quyền người dùng</th>
            <th>Thao tác</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="account in accountSearch" :key="account.id">
            <td>U{{ account.id }}</td>
            <td>{{ account.profile.name }}</td>
            <td>{{ account.profile.phone ?? 'Chưa có SĐT' }}</td>
            <td>{{ account.profile.email ?? 'Chưa có email' }}</td>
            <td>
              <button
                v-if="account.status == 'actived'"
                type="submit"
                class="btn btn-success text-white text-decoration-none m-1"
                @click="changeAccountStauts(account.id, account.status)"
              >
                Đang hoạt động
              </button>
              <button
                v-else
                type="submit"
                class="btn btn-danger text-white text-decoration-none m-1"
                @click="changeAccountStauts(account.id, account.status)"
              >
                Đang khóa
              </button>
            </td>
            <td>
              {{ account.role == 'user' ? 'Người dùng' : 'Quản trị' }}
            </td>
            <td>
              <router-link :to="{ name: 'profile', params: { user_id: account.id } }">
                <i>Xem trang cá nhân</i>
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else class="mt-3">
        <h5>Không tìm thấy tài khoản</h5>
      </div>

      <nav v-if="pagination">
        <ul class="pagination justify-content-center">
          <!-- Trang trước -->
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <a class="page-link" href="#" @click.prevent="goToPage(currentPage - 1)">«</a>
          </li>

          <!-- Các trang -->
          <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: currentPage === page }">
            <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
          </li>

          <!-- Trang sau -->
          <li class="page-item" :class="{ disabled: currentPage === lastPage }">
            <a class="page-link" href="#" @click.prevent="goToPage(currentPage + 1)">»</a>
          </li>
        </ul>
      </nav>
    </div>
  </main>
</template>

<script setup>
import axios from 'axios'
import { computed, onMounted, ref } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const accounts = ref([])
onMounted(() => {
  getAccounts()
})

// Phân trang
const pagination = ref()
const currentPage = ref()
const lastPage = ref()
const getAccounts = async (page = 1) => {
  const res = await axios.get(`/admin/account/getAccounts?page=${page}`)
  console.log(res.data)
  // Thông tin phân trang
  pagination.value = res.data.pagination
  currentPage.value = res.data.pagination.current_page
  lastPage.value = res.data.pagination.last_page

  // Thông tin tài khoản
  accounts.value = res.data.pagination.data
}

const goToPage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    getAccounts(page)
  }
}

const visiblePages = computed(() => {
  const delta = 2
  const from = Math.max(1, currentPage.value - delta)
  const to = Math.min(lastPage.value, currentPage.value + delta)
  const pages = []
  for (let i = from; i <= to; i++) {
    pages.push(i)
  }
  return pages
})

// Search
const nameSearch = ref('')
const statusSearch = ref('')
const accountSearch = computed(() =>
  accounts.value.filter((account) => {
    let result = account.profile.name.toLowerCase().includes(nameSearch.value.toLowerCase())
    if (statusSearch.value != '')
      result =
        account.profile.name.toLowerCase().includes(nameSearch.value.toLowerCase()) &&
        account.status == statusSearch.value
    return result
  })
)

// Khóa tài khoản
async function changeAccountStauts(accountId, status) {
  let statusChange = status == 'actived' ? 'disabled' : 'actived'
  let confirm = ''
  if (statusChange == 'actived')
    confirm = 'Tài khoản sẽ được mở khóa\nNhững người dùng khác có thể xem và tìm kiếm\nChắc chắn thay đổi trạng thái?'
  else confirm = 'Tài khoản sẽ bị khóa\nNhững người dùng khác không thể xem và tìm kiếm\nChắc chắn thay đổi trạng thái?'

  if (window.confirm(confirm)) {
    let formData = new FormData()
    formData.append('user_id', accountId)
    formData.append('status', statusChange)
    try {
      let res = await axios.post(`admin/account/changeAccountStatus`, formData)
      accounts.value.find((account) => account.id == accountId).status = res.data.status
      toast.success(res.data.message, {
        position: 'bottom-right',
      })
    } catch (error) {
      console.log('Thay đổi trạng thái thất bại!', error)
      toast.error(error.response.data.message, {
        position: 'bottom-right',
      })
    }
  } else {
    return
  }
}
</script>
<style scoped>
</style>
