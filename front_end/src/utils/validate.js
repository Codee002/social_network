import dayjs from 'dayjs'

function email(email) {
  // Bắt buộc
  if (!email || email.trim().length == 0) {
    return 'Email không được để trống'
  }

  // Regex
  const emailRegex = /^[\w-.]+@([\w-]+\.)+[\w-]{2,4}$/
  if (!emailRegex.test(email)) return 'Email không hợp lệ'

  return null
}

function username(username) {
  // Bắt buộc
  if (!username || username.trim().length == 0) return 'Tên đăng nhập không được để trống'

  if (username.trim().length < 4 || username.trim().length > 32) return 'Tên đăng nhập phải có độ dài từ 4 - 32 ký tự'

  // Tên đăng nhập chỉ có chữ và số
  const usernameRegex = /^[a-zA-Z0-9]*$/
  if (!usernameRegex.test(username)) return 'Tên đăng nhập không chứa ký tự đặc biệt'

  // Ký tự đầu
  const regexFirstChar = /^[A-Za-z]{1,}/
  if (!regexFirstChar.test(username)) return 'Tên đăng nhập bắt đầu phải là chữ cái'

  return null
}

function name(name) {
  // Bắt buộc
  if (!name || name.trim().length == 0) return 'Họ tên không được để trống'

  const nameRegex = /^[\p{L}\p{M}\s]+$/u
  if (!nameRegex.test(name)) return 'Họ tên không hợp lệ!'
  return null
}

function password(password) {
  if (!password || password.trim().length == 0) return 'Mật khẩu không được để trống'

  if (password.length < 8 || password.length > 20) return 'Mật khẩu phải từ 8 đến 20 ký tự'

  return null
}

function passwordConfirm(password, passwordConfirm) {
  if (!passwordConfirm || passwordConfirm.trim().length == 0) return 'Mật khẩu nhập lại không được để trống'

  if (password != passwordConfirm) return 'Mật khẩu nhập lại không đúng'

  return null
}

function phone(phoneNumber) {
  if (!phoneNumber || phoneNumber.trim().length == 0) return 'Số điện thoại không được để trống'
  const phoneRegex = /^(03|05|07|08|09|01[2|6|8|9])+([0-9]{8})\b$/
  if (!phoneRegex.test(phoneNumber)) return 'Số điện thoại không hợp lệ'

  return null
}

function address(address) {
  // Bắt buộc
  if (!address || address.trim().length == 0) return 'Quê quán không được để trống'

  const addressRegex = /^[\p{L}\p{M}\s]+$/u
  if (!addressRegex.test(address)) return 'Quê quán không hợp lệ!'
  return null
}

function birthday(day) {
  if (!day) return 'Ngày sinh không được trống'
  const today = dayjs().startOf('day')
  const birthDate = dayjs(day)
  if (!birthDate.isBefore(today)) return 'Ngày sinh phải nhỏ hơn ngày hiện tại'

  return null
}

function gender(gender) {
  if (!gender || gender.trim().length == 0) return 'Giới tính không được để trống'
  return null
}

export default {
  username,
  email,
  name,
  password,
  passwordConfirm,
  phone,
  address,
  birthday,
  gender,
}
