// import AdminLayout from '@/layouts/partials/AdminLayout'
import AdminLayout from '../layouts/AdminLayout'
import AccountPage from '@/pages/admin/accounts/AccountPage'
import AccountReport from '@/pages/admin/accounts/ReportPage'
import PostPage from '@/pages/admin/posts/PostPage'
import PostReport from '@/pages/admin/posts/ReportPage'

const adminRoutes = [
  {
    path: '/admin',
    component: AdminLayout,
    meta: {
      requiresAuth: true,
      requiresAdmin: true,
    },
    children: [
      {
        path: 'post',
        component: PostPage,
        name: 'admin.post',
        meta: {
          requiresAuth: true,
          requiresAdmin: true,
          title: 'Admin Post',
        },
      },
      {
        path: 'post/report',
        component: PostReport,
        name: 'admin.post.report',
        meta: {
          requiresAuth: true,
          requiresAdmin: true,
          title: 'Admin Post Report',
        },
      },
      {
        path: 'account',
        component: AccountPage,
        name: 'admin.account',
        meta: {
          requiresAuth: true,
          requiresAdmin: true,
          title: 'Admin Account',
        },
      },
      {
        path: 'account/report',
        component: AccountReport,
        name: 'admin.account.report',
        meta: {
          requiresAuth: true,
          requiresAdmin: true,
          title: 'Admin Account Report',
        },
      },
    ],
  },
]

export default adminRoutes
