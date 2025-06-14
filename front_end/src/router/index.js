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

  if (to.meta.requiresAuth && !isLogin) {
    return next('/auth/login')
  }

  if (isLogin && publicRoutes.includes(to.path)) {
    return next('/home')
  }

  next()
})

router.afterEach((to) => {
  const defaultTitle = 'Zync'
  document.title = to.meta.title ? `${to.meta.title} | ${defaultTitle}` : defaultTitle
})

export default router
