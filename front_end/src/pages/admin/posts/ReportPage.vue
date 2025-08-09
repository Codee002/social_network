<template>
  <main class="app-main p-0" style="height: 100%">
    <div class="container" style="width: 90%">
      <h2 class="text-center fw-bolder mt-4 mb-4">Danh sách tố cáo bài viết</h2>
      <div class="d-flex align-items-center mb-1 row">
        <div class="col-6">
          <div class="form-group d-flex">
            <input placeholder="Tên người tố cáo" v-model="nameSend" class="form-control me-1" />
            <input placeholder="Mã bài viết" v-model="idPost" class="form-control me-1" />
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
            <th>Bài viết tố cáo</th>
            <th>Phương tiện</th>
            <th>Tiêu đề</th>
            <th>Lý do</th>
            <th>Thời gian</th>
            <th>Trạng thái</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="report in reportSearch" :key="report.id">
            <td>RP{{ report.id }}</td>
            <td>
              <router-link :to="{ name: 'profile', params: { user_id: report.user.id } }">
                {{ report.user.profile.name }}
              </router-link>
            </td>
            <td>
              <router-link :to="{ name: 'post', params: { post_id: report.post.id } }">
                P{{ report.post.id }}
              </router-link>
            </td>
            <td>
              <div class="d-inline" v-if="report.post.medias.length != 0">
                <img
                  v-if="report.post.medias[0].type == 'image'"
                  :src="$backendBaseUrl + report.post.medias[0].path"
                  alt=""
                  width="75rem"
                />
                <video
                  v-if="report.post.medias[0].type == 'video'"
                  :src="$backendBaseUrl + report.post.medias[0].path"
                  alt=""
                  width="75rem"
                />
              </div>
              <div v-else class="d-inline">
                <i>Không có phương tiện</i>
              </div>
            </td>
            <td>{{ report.post.caption == '' ? 'Chưa có tiều đề' : report.post.caption }}</td>
            <td>{{ report.content }}</td>
            <td>{{ $dayjs(report.created_at).format('MM/DD/YYYY h:mm A') }}</td>
            <td>
              <button
                v-if="report.post.status == 'actived'"
                type="submit"
                class="btn btn-success text-white text-decoration-none m-1"
                @click="changePostStauts(report.post.id, report.post.status)"
              >
                Đang hoạt động
              </button>
              <button
                v-else
                type="submit"
                class="btn btn-danger text-white text-decoration-none m-1"
                @click="changePostStauts(report.post.id, report.post.status)"
              >
                Đang khóa
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else class="mt-3">
        <h5>Không tìm thấy bài viết</h5>
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
  getReportPosts()
})

// Phân trang
const pagination = ref()
const currentPage = ref()
const lastPage = ref()
const getReportPosts = async (page = 1) => {
  try {
    const res = await axios.get(`/admin/post/getReportPosts?page=${page}`)
    console.log(res.data)
    // Thông tin phân trang
    pagination.value = res.data.pagination
    currentPage.value = res.data.pagination.current_page
    lastPage.value = res.data.pagination.last_page

    // Thông tin tài khoản
    reports.value = res.data.pagination.data
  } catch (error) {
    console.log('Có lỗi khi lấy bài viết', error)
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    getReportPosts(page)
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
const idPost = ref('')
const statusSearch = ref('')
const reportSearch = computed(() =>
  reports.value.filter((report) => {
    let result =
      report.user.profile.name.toLowerCase().includes(nameSend.value.toLowerCase()) &&
      report.post.id.toString().includes(idPost.value)
    if (statusSearch.value != '')
      result =
        report.user.profile.name.toLowerCase().includes(nameSend.value.toLowerCase()) &&
        report.post.id.toString().includes(idPost.value) &&
        report.post.status == statusSearch.value
    return result
  })
)

// Khóa tài khoản
async function changePostStauts(postId, status) {
  let statusChange = status == 'actived' ? 'disabled' : 'actived'
  let confirm = ''
  if (statusChange == 'actived')
    confirm = 'Bài viết sẽ được mở khóa\nNhững người dùng khác có thể xem và tìm kiếm\nChắc chắn thay đổi trạng thái?'
  else confirm = 'Bài viết sẽ bị khóa\nNhững người dùng khác không thể xem và tìm kiếm\nChắc chắn thay đổi trạng thái?'

  if (window.confirm(confirm)) {
    let formData = new FormData()
    formData.append('post_id', postId)
    formData.append('status', statusChange)
    try {
      let res = await axios.post(`admin/post/changePostStatus`, formData)
      reports.value.find((report) => report.post.id == postId).post.status = res.data.status
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
