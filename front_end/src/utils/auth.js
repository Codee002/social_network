import axios from 'axios'
// import router from '@/router'

function isLogin() {
  return !!localStorage.getItem('auth_token')
}

async function logout() {
  try {
    await axios.post(`/auth/logout`)

    localStorage.removeItem('auth_token')

    // router.push({ name: 'auth.login' })
  } catch (error) {
    console.error('Đăng xuất thất bại:', error)
  }
}

async function getOwner() {
  const res = await axios.get(`/owner`)
  return res.data.user
}

function getRelationStatus(relation) {
  if (relation == 'stranger' || relation.status == 'reject') {
    return 'stranger'
  } else if (relation.type == 'friend') {
    if (relation.status == 'completed') {
      return 'friend'
    } else {
      return 'friend_pending'
    }
  }

  return 'owner'
}

export default { isLogin, logout, getOwner, getRelationStatus }
