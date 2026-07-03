import React from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

export default function Dashboard() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()

  async function handleLogout() {
    await logout()
    navigate('/login', { replace: true })
  }

  return (
    <div className="min-h-screen bg-slate-50 text-black">
      <div className="mx-auto max-w-5xl px-6 py-6">
        <nav className="flex items-center justify-between border-b border-black/5 py-5">
          <div className="flex items-center gap-3">
            <div className="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#FF2D20] text-white font-bold">E</div>
            <div>
              <p className="text-base font-semibold">ERP Core</p>
              <p className="text-xs text-black/50">API-first ERP platform</p>
            </div>
          </div>
          <button
            type="button"
            onClick={handleLogout}
            className="rounded-full px-4 py-2 text-sm font-medium text-black ring-1 ring-black/10 transition hover:bg-black/5"
          >
            Logout
          </button>
        </nav>

        <main className="mt-10">
          <div className="rounded-[30px] border border-black/5 bg-white p-8 shadow-[0_20px_80px_rgba(15,23,42,0.08)]">
            <p className="text-sm font-semibold uppercase tracking-[0.18em] text-[#FF2D20]">Dashboard</p>
            <h1 className="mt-4 text-3xl font-bold text-slate-950">
              Welcome{user?.name ? `, ${user.name}` : ''}
            </h1>
            <p className="mt-2 text-sm text-black/60">
              This is a placeholder protected dashboard. Signed in as{' '}
              <span className="font-medium text-black">{user?.email}</span>
              {user?.role ? ` (${user.role})` : ''}.
            </p>
          </div>
        </main>
      </div>
    </div>
  )
}
