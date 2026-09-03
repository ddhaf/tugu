import api from './api'

export const getCurrentUser = async () => {
  const response = await api.get('/me')

  return response.data.user
}