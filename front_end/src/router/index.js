import { createRouter, createWebHistory } from 'vue-router'
import auth from '@/utils/auth.js'
import adminRoutes from './admin'
import authRoutes from './auth'
import userRoutes from './user'

const routes = [...adminRoutes, ...authRoutes, ...userRoutes]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const publicRoutes = ['/auth/login', '/auth/register', '/auth/forgot']
  const isLogin = auth.isLogin()

  // Xét đăng nhập
  if (to.meta.requiresAuth && !isLogin) {
    return next('/auth/login')
  }

  let userRole = 'guest'
  let userStatus = 'actived'
  // Nếu đã đăng nhập thì lấy thông tin user
  if (isLogin) {
    const user = JSON.parse(localStorage.getItem('owner'))
    console.log(user)
    userRole = user.role
    userStatus = user.status
  }

  // Nếu tài khoản bị khóa
  if (isLogin && userStatus == 'disabled' && to.path !== '/account/ban') {
    console.log("Bị khóa tài khoản")
    next('/account/ban')
  }

  // Không cho phép chủ động vào trang ban
  if (to.path == '/account/ban' && userStatus != 'disabled') {
    next('/home')
  }

  // Đang đăng nhập thì k truy cập được route Auth
  if (isLogin && publicRoutes.includes(to.path)) {
    if (userRole == 'user') return next('/home')
    else if (userRole == 'admin') return next('/admin/account')
  }

  // Không cho phép truy cập route admin trái phép
  if (to.meta.requiresAdmin && userRole != 'admin') {
    return next('/home')
  }

  next()
})

router.afterEach((to) => {
  const defaultTitle = 'Zync'
  document.title = to.meta.title ? `${to.meta.title}` : defaultTitle
})

export default router
