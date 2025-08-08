import AuthLayout from '../layouts/AuthLayout'
import Login from '../pages/auth/LoginPage'
import Register from '../pages/auth/RegisterPage'
import Forgot from '../pages/auth/ForgotPage'
import SendBy from '../pages/auth/SendBy'
import TokenCheck from '../pages/auth/TokenCheck'
import TokenError from '../pages/auth/TokenError'
import ResetPage from '../pages/auth/ResetPage'
import EmailActive from '../pages/auth/EmailActive'
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
        path: 'sendby',
        component: SendBy,
        name: 'auth.sendby',
        meta: {
          title: 'Chọn phương thức gửi mã',
          requiresAuth: false,
        },
      },
      {
        path: 'checkToken/:type/:user_id/:token',
        component: TokenCheck,
        name: 'auth.checkToken',
        meta: {
          title: 'Quên mật khẩu',
          requiresAuth: false,
        },
      },
      {
        path: 'checkToken/error',
        component: TokenError,
        name: 'auth.tokenError',
        meta: {
          title: 'Liên kết không tồn tại',
          // requiresAuth: false,
        },
      },
      {
        path: 'reset',
        component: ResetPage,
        name: 'auth.reset',
        meta: {
          title: 'Đặt lại mật khẩu',
          requiresAuth: false,
        },
      },

      // Xac thuc email
      {
        path: 'emailActive',
        component: EmailActive,
        name: 'setting.emailActive',
        meta: {
          title: 'Xác thực email',
          requiresAuth: true,
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
