<template>
  <main class="app-main p-0" style="height: 100%">
    <div class="container" style="width: 90%">
      <h2 class="text-center fw-bolder mt-4 mb-4">Danh sách tố cáo tài khoản</h2>
      <div class="d-flex align-items-center mb-1 row">
        <div class="col-6">
          <div class="form-group d-flex">
            <input placeholder="Tên người tố cáo" v-model="nameSend" class="form-control me-1" />
            <input placeholder="Tên người bị tố cáo" v-model="nameReceive" class="form-control me-1" />
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

      <table class="table table-bordered table-striped mt-3" v-if="reportSearch.length != 0">
        <thead>
          <tr>
            <th>Mã</th>
            <th>Tài khoản tố cáo</th>
            <th>Tài khoản bị tố cáo</th>
            <th>Lý do</th>
            <th>Thời gian</th>
            <th>Trạng thái</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="report in reportSearch" :key="report.id">
            <td>RU{{ report.id }}</td>
            <td>
              <router-link :to="{ name: profile, params: { user_id: report.user.id } }">
                {{ report.user.profile.name }}
              </router-link>
            </td>
            <td>
              <router-link :to="{ name: profile, params: { user_id: report.receiver.id } }">
                {{ report.receiver.profile.name }}
              </router-link>
            </td>
            <td>{{ report.content }}</td>
            <td>{{ $dayjs(report.created_at).format('MM/DD/YYYY h:mm A') }}</td>
            <td>
              <button
                v-if="report.receiver.status == 'actived'"
                type="submit"
                class="btn btn-success text-white text-decoration-none m-1"
                @click="changeAccountStauts(report.receiver.id, report.receiver.status)"
              >
                Đang hoạt động
              </button>
              <button
                v-else
                type="submit"
                class="btn btn-danger text-white text-decoration-none m-1"
                @click="changeAccountStauts(report.receiver.id, report.receiver.status)"
              >
                Đang khóa
              </button>
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
const reports = ref([])

onMounted(() => {
  getReportAccounts()
})

// Phân trang
const pagination = ref()
const currentPage = ref()
const lastPage = ref()
const getReportAccounts = async (page = 1) => {
  try {
    const res = await axios.get(`/admin/account/getReportAccounts?page=${page}`)
    console.log(res.data)
    // Thông tin phân trang
    pagination.value = res.data.pagination
    currentPage.value = res.data.pagination.current_page
    lastPage.value = res.data.pagination.last_page

    // Thông tin tài khoản
    reports.value = res.data.pagination.data
  } catch (error) {
    console.log('Có lỗi khi lấy danh sách tài khoản', error)
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    getReportAccounts(page)
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
const nameSend = ref('')
const nameReceive = ref('')
const statusSearch = ref('')
const reportSearch = computed(() =>
  reports.value.filter((report) => {
    let result =
      report.receiver.profile.name.toLowerCase().includes(nameReceive.value.toLowerCase()) &&
      report.user.profile.name.toLowerCase().includes(nameSend.value.toLowerCase())
    if (statusSearch.value != '')
      result =
        report.receiver.profile.name.toLowerCase().includes(nameReceive.value.toLowerCase()) &&
        report.user.profile.name.toLowerCase().includes(nameSend.value.toLowerCase()) &&
        report.receiver.status == statusSearch.value
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
      reports.value.find((report) => report.receiver.id == accountId).receiver.status = res.data.status
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
