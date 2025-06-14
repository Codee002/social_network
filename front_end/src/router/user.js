import MainLayout from '../layouts/MainLayout'
import DashboardPage from '../pages/user/DashboardPage'
import FriendPage from '../pages/user/FriendPage'
import ProfilePage from '../pages/user/ProfilePage'
import FavoritePage from '../pages/user/FavoritePage'
import ConversationPage from '../pages/user/ConversationPage'
import ChatArea from '@/pages/user/conversations/ChatArea'

const userRoutes = [
  {
    path: '/',
    component: MainLayout,
    meta: {
      requiresAuth: true,
    },
    children: [
      {
        path: 'home',
        component: DashboardPage,
        name: 'home',
        meta: {
          title: 'Trang chủ',
          requiresAuth: true,
        },
      },
      {
        path: 'friend',
        component: FriendPage,
        name: 'friend',
        meta: {
          title: 'Đăng nhập',
          requiresAuth: true,
        },
      },
      {
        path: 'profile/:user_id',
        component: ProfilePage,
        name: 'profile',
        meta: {
          title: 'Trang cá nhân',
          requiresAuth: true,
        },
      },
      {
        path: 'favorite',
        component: FavoritePage,
        name: 'favorite',
        meta: {
          title: 'Yêu thích',
          requiresAuth: true,
        },
      },
      {
        path: 'conversation',
        component: ConversationPage,
        name: 'conversation',
        meta: {
          title: 'Tin nhắn',
          requiresAuth: true,
        },
        children: [
          {
            path: ':conversation_id',
            component: ChatArea,
            name: 'conversation.chat',
            meta: {
              requiresAuth: true,
            },
          },
        ],
      },
    ],
  },
]

export default userRoutes
