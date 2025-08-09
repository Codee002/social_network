<template>
  <main class="app-main p-0" style="height: 100%">
    <div class="container" style="width: 90%">
      <h2 class="text-center fw-bolder mt-4 mb-4">Danh sách bài viết</h2>
      <div class="d-flex align-items-center mb-1 row">
        <div class="col-6">
          <div class="form-group d-flex">
            <input placeholder="Tiêu đề" v-model="captionSearch" class="form-control me-1" />
            <select class="form-select me-1" v-model="statusSearch">
              <option value="" disabled="" selected="">Trạng thái</option>
              <option value="actived">Đang hoạt động</option>
              <option value="disabled">Đang khóa</option>
            </select>

            <!-- <select class="form-select me-1">
              <option value="" disabled="" selected="">Vi phạm</option>
              <option value="actived">Có</option>
              <option value="disabled">Không</option>
            </select> -->

            <button type="submit" class="btn btn-primary text-white text-decoration-none m-1">Tìm</button>
          </div>
        </div>
        <div class="text-end col-6">
          <!-- <a href="http://vibez.localhost/admin/account/export" class="btn btn-success text-white text-end ms-3">
            Xuất Excel
          </a> -->
        </div>
      </div>

      <table class="table table-bordered table-striped mt-3" v-if="postSearch.length != 0">
        <thead>
          <tr>
            <th>Mã</th>
            <th>Phương tiện</th>
            <th>Tiêu đề</th>
            <th>Lượt xem</th>
            <th>Lượt thích</th>
            <th>Lượt bình luận</th>
            <th>Lượt chia sẻ</th>
            <th>Lượt tố cáo</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="post in postSearch" :key="post.id">
            <td>P{{ post.id }}</td>
            <td>
              <div class="d-inline" v-if="post.medias.length != 0">
                <img
                  v-if="post.medias[0].type == 'image'"
                  :src="$backendBaseUrl + post.medias[0].path"
                  alt=""
                  width="75rem"
                />
                <video
                  v-if="post.medias[0].type == 'video'"
                  :src="$backendBaseUrl + post.medias[0].path"
                  alt=""
                  width="75rem"
                />
              </div>
              <div v-else class="d-inline">
                <i>Không có phương tiện</i>
              </div>
            </td>
            <td>{{ post.caption == '' ? 'Chưa có tiều đề' : post.caption }}</td>
            <td>{{ views[post.id].length }}</td>
            <td>{{ likes[post.id].length }}</td>
            <td>{{ comments[post.id].length }}</td>
            <td>{{ shares[post.id].length }}</td>
            <td>{{ reports[post.id].length }}</td>
            <td>
              <button
                v-if="post.status == 'actived'"
                type="submit"
                class="btn btn-success text-white text-decoration-none m-1"
                @click="changePostStauts(post.id, post.status)"
              >
                Đang hoạt động
              </button>
              <button
                v-else
                type="submit"
                class="btn btn-danger text-white text-decoration-none m-1"
                @click="changePostStauts(post.id, post.status)"
              >
                Đang khóa
              </button>
            </td>
            <td>
              <router-link :to="{ name: 'post', params: { post_id: post.id } }">
                <i>Xem bài viết</i>
              </router-link>
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
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const posts = ref([])
const views = ref([])
const likes = ref([])
const shares = ref([])
const reports = ref([])
const comments = ref([])
const pagination = ref({})
const currentPage = ref(1)
const lastPage = ref(1)

const getPosts = async (page = 1) => {
  try {
    const res = await axios.get(`/admin/post/getPosts?page=${page}`)
    console.log(res.data)
    // Thông tin phân trang
    pagination.value = res.data.pagination
    currentPage.value = res.data.pagination.current_page
    lastPage.value = res.data.pagination.last_page

    // Thông tin bài viết
    posts.value = res.data.pagination.data
    console.log(posts.value)
    views.value = res.data.views
    likes.value = res.data.likes
    comments.value = res.data.comments
    shares.value = res.data.shares
    reports.value = res.data.reports
  } catch (error) {
    console.log('Có lỗi khi lấy bài viết', error)
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    getPosts(page)
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

onMounted(() => {
  getPosts()
})

// Search
const captionSearch = ref('')
const statusSearch = ref('')
const postSearch = computed(() =>
  posts.value.filter((post) => {
    if (!post.caption) post.caption = ''
    let result = []
    result = post.caption.toLowerCase().includes(captionSearch.value.toLowerCase())
    if (statusSearch.value != '')
      result =
        post.caption.toLowerCase().includes(captionSearch.value.toLowerCase()) && post.status == statusSearch.value
    return result
  })
)

// Khóa bài viết
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
      posts.value.find((post) => post.id == postId).status = res.data.status
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
