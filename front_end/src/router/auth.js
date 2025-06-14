import AuthLayout from '../layouts/AuthLayout'
import Login from '../pages/auth/LoginPage'
import Register from '../pages/auth/RegisterPage'
import Forgot from '../pages/auth/ForgotPage'
import NotFound from '../pages/errors/NotFound'

const authRoutes = [
  {
    path: '/auth',
    component: AuthLayout,
    children: [
      {
        path: 'login',
        component: Login,
        name: 'auth.login',
        meta: {
          title: 'Đăng nhập',
          requiresAuth: false,
        },
      },
      {
        path: 'register',
        component: Register,
        name: 'auth.register',
        meta: {
          title: 'Đăng ký',
          requiresAuth: false,
        },
      },
      {
        path: 'forgot',
        component: Forgot,
        name: 'auth.forgot',
        meta: {
          title: 'Quên mật khẩu',
          requiresAuth: false,
        },
      },
      {
        path: ':pathMatch(.*)*',
        component: NotFound,
        name: 'not-found',
        meta: {
          title: 'Page Not Found',
          requiresAuth: false,
        },
      },
    ],
  },
]

export default authRoutes
