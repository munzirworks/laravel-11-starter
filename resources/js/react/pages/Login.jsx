import React, { useState } from 'react'
import { Link, useLocation, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

export default function Login() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const location = useLocation()
  const [form, setForm] = useState({ email: '', password: '' })
  const [errors, setErrors] = useState({})
  const [error, setError] = useState('')
  const [submitting, setSubmitting] = useState(false)

  const redirectTo = location.state?.from?.pathname || '/app/dashboard'

  function handleChange(event) {
    const { name, value } = event.target
    setForm((prev) => ({ ...prev, [name]: value }))
  }

  async function handleSubmit(event) {
    event.preventDefault()
    setError('')
    setErrors({})
    setSubmitting(true)

    try {
      await login(form.email, form.password)
      navigate(redirectTo, { replace: true })
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {})
      } else if (err.response?.status === 401) {
        setError(err.response.data.message || 'Invalid credentials.')
      } else {
        setError('Something went wrong. Please try again.')
      }
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <div className="min-h-screen bg-slate-50 text-black">
      <div className="mx-auto flex min-h-screen max-w-md flex-col justify-center px-6 py-12">
        <div className="mb-8 flex items-center gap-3">
          <div className="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#FF2D20] text-white font-bold">E</div>
          <div>
            <p className="text-base font-semibold">ERP Core</p>
            <p className="text-xs text-black/50">API-first ERP platform</p>
          </div>
        </div>

        <div className="rounded-[30px] border border-black/5 bg-white p-8 shadow-[0_20px_80px_rgba(15,23,42,0.08)]">
          <h1 className="text-2xl font-bold text-slate-950">Sign in</h1>
          <p className="mt-2 text-sm text-black/60">Welcome back. Enter your credentials to continue.</p>

          {error && (
            <div className="mt-6 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-100">
              {error}
            </div>
          )}

          <form onSubmit={handleSubmit} className="mt-6 space-y-4">
            <div>
              <label htmlFor="email" className="block text-sm font-medium text-slate-900">
                Email
              </label>
              <input
                id="email"
                name="email"
                type="email"
                autoComplete="email"
                required
                value={form.email}
                onChange={handleChange}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.email && <p className="mt-1 text-xs text-red-600">{errors.email[0]}</p>}
            </div>

            <div>
              <label htmlFor="password" className="block text-sm font-medium text-slate-900">
                Password
              </label>
              <input
                id="password"
                name="password"
                type="password"
                autoComplete="current-password"
                required
                value={form.password}
                onChange={handleChange}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.password && <p className="mt-1 text-xs text-red-600">{errors.password[0]}</p>}
            </div>

            <button
              type="submit"
              disabled={submitting}
              className="w-full rounded-full bg-black px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-900 disabled:opacity-60"
            >
              {submitting ? 'Signing in…' : 'Sign in'}
            </button>
          </form>

          <p className="mt-6 text-center text-sm text-black/60">
            Don&apos;t have an account?{' '}
            <Link to="/register" className="font-semibold text-[#FF2D20] hover:underline">
              Create one
            </Link>
          </p>
        </div>

        <p className="mt-8 text-center text-sm">
          <Link to="/" className="text-black/50 hover:underline">
            &larr; Back to home
          </Link>
        </p>
      </div>
    </div>
  )
}
