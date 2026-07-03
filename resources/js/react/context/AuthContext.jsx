import React, { createContext, useCallback, useContext, useEffect, useState } from 'react'
import apiClient, { getStoredToken, setStoredToken } from '../api/client'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [token, setToken] = useState(() => getStoredToken())
  const [user, setUser] = useState(null)
  // Whether we're still resolving the initial auth state (checking an
  // existing token against the API before rendering protected routes).
  const [loading, setLoading] = useState(true)

  const fetchUser = useCallback(async () => {
    try {
      const { data } = await apiClient.get('/me')
      setUser(data)
      return data
    } catch (error) {
      setToken(null)
      setStoredToken(null)
      setUser(null)
      throw error
    }
  }, [])

  useEffect(() => {
    if (!token) {
      setLoading(false)
      return
    }

    fetchUser().finally(() => setLoading(false))
    // Only re-run this effect when the token itself changes.
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [token])

  const login = useCallback(async (email, password) => {
    const { data } = await apiClient.post('/login', { email, password })
    setStoredToken(data.access_token)
    setToken(data.access_token)
    setUser(data.user)
    return data.user
  }, [])

  const register = useCallback(async (name, email, password, passwordConfirmation) => {
    const { data } = await apiClient.post('/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    })
    setStoredToken(data.access_token)
    setToken(data.access_token)
    setUser(data.user)
    return data.user
  }, [])

  const logout = useCallback(async () => {
    try {
      await apiClient.post('/logout')
    } catch (error) {
      // Ignore network/API errors on logout — we still want to clear
      // local state so the user is signed out on this device.
    } finally {
      setStoredToken(null)
      setToken(null)
      setUser(null)
    }
  }, [])

  const value = {
    token,
    user,
    isAuthenticated: Boolean(token),
    loading,
    login,
    register,
    logout,
  }

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const context = useContext(AuthContext)
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return context
}
