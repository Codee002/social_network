import axios from 'axios'
// import router from '@/router'
import configs from '@/utils/configs'

function isLogin() {
  return !!localStorage.getItem('auth_token')
}

async function logout() {
  try {
    await axios.post(
      `${configs.pathApi}/auth/logout`,
      {},
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
        },
      },
    )

    localStorage.removeItem('auth_token')

    // router.push({ name: 'auth.login' })
  } catch (error) {
    console.error('Đăng xuất thất bại:', error)
  }
}

async function getOwner() {
  const res = await axios.get(`${configs.pathApi}/owner`, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
    },
  })
  return res.data.user
}

export default { isLogin, logout, getOwner }
