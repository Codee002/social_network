import MainLayout from '../layouts/MainLayout'
import DashboardPage from '../pages/user/DashboardPage'
import FriendPage from '../pages/user/FriendPage'
import ProfilePage from '../pages/user/ProfilePage'
import FavoritePage from '../pages/user/FavoritePage'
import ConversationPage from '../pages/user/ConversationPage'
import ChatArea from '@/pages/user/conversations/ChatArea'
import PostPage from '@/pages/user/PostPage'
import StoryDetail from '@/pages/user/story/StoryDetail'
import CallPage from '@/pages/user/conversations/CallPage'
import SettingPage from '@/pages/user/SettingPage'
import SettingInfo from '@/pages/user/settings/SettingInfo'
import SettingSecurity from '@/pages/user/settings/SettingSecurity'
import SettingTheme from '@/pages/user/settings/SettingTheme'
import ProfileSearch from '@/pages/user/search/ProfileSearch'
import AccountBan from '@/pages/user/account_ban/AccountBan'

const userRoutes = [
  {
    path: '/',
    component: MainLayout,
    meta: {
      requiresAuth: true,
    },
    // Trang home
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

      // Trang bạn bè
      {
        path: 'friend',
        component: FriendPage,
        name: 'friend',
        meta: {
          title: 'Bạn bè',
          requiresAuth: true,
        },
      },

      // Trang cá nhân
      {
        path: 'profile/:user_id',
        component: ProfilePage,
        name: 'profile',
        meta: {
          title: 'Trang cá nhân',
          requiresAuth: true,
        },
      },

      // Trang yêu thích
      {
        path: 'favorite',
        component: FavoritePage,
        name: 'favorite',
        meta: {
          title: 'Yêu thích',
          requiresAuth: true,
        },
      },

      // Trang nhắn tin
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

      // Trang bài viết
      {
        path: 'post/:post_id',
        component: PostPage,
        name: 'post',
        meta: {
          title: 'Bài viết',
          requiresAuth: true,
        },
      },

      // Trang tin
      {
        path: 'story/:story_id',
        component: StoryDetail,
        name: 'story',
        meta: {
          title: 'Story',
          requiresAuth: true,
        },
      },

      // Trang cài đặt
      {
        path: 'setting',
        component: SettingPage,
        name: 'setting',
        meta: {
          title: 'Cài đặt',
          requiresAuth: true,
        },
        children: [
          // Trang cài đặt thông tin cá nhân
          {
            path: 'info',
            component: SettingInfo,
            name: 'setting.info',
            meta: {
              requiresAuth: true,
            },
          },
          // Trang cài đặt bảo mật
          {
            path: 'security',
            component: SettingSecurity,
            name: 'setting.security',
            meta: {
              requiresAuth: true,
            },
          },

          // Trang cài đặt giao diện
          {
            path: 'theme',
            component: SettingTheme,
            name: 'setting.theme',
            meta: {
              requiresAuth: true,
            },
          },
        ],
      },

      // Trang thông báo tài khoản bị ban
      {
        path: 'account/ban',
        component: AccountBan,
        name: 'account.ban',
        meta: {
          title: 'Tài khoản bị khóa',
          requiresAuth: true,
        },
      },

      // Trang tìm kiếm
      {
        path: 'search/:input',
        component: ProfileSearch,
        name: 'search',
        meta: {
          title: 'Tìm kiếm',
          requiresAuth: true,
        },
      },
    ],
  },

  // Trang call video
  {
    path: '/call-window',
    component: CallPage,
    name: 'call',
    meta: {
      title: 'Cuộc gọi',
      requiresAuth: true,
    },
  },
]

export default userRoutes
