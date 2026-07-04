import React, { useMemo, useState } from 'react'
import { useAuth } from '../../context/AuthContext'

function roleFromUser(user) {
  if (!user) {
    return ''
  }

  if (typeof user.role === 'string') {
    return user.role.toLowerCase()
  }

  if (typeof user.role?.value === 'string') {
    return user.role.value.toLowerCase()
  }

  return ''
}

export default function Topbar({ onToggleSidebar }) {
  const { user, logout } = useAuth()
  const [loggingOut, setLoggingOut] = useState(false)
  const role = roleFromUser(user)

  const badgeClass = useMemo(() => {
    if (role === 'admin') {
      return 'inline-flex items-center rounded-full bg-[#FF2D20]/10 px-2.5 py-0.5 text-xs font-medium text-[#FF2D20]'
    }

    return 'inline-flex items-center rounded-full bg-black/5 px-2.5 py-0.5 text-xs font-medium text-black/70'
  }, [role])

  const roleText = role === 'admin' ? 'Administrator' : 'Staff'

  async function handleLogout() {
    if (loggingOut) {
      return
    }

    setLoggingOut(true)

    try {
      await logout()
    } finally {
      window.location.href = '/login'
    }
  }

  return (
    <header className="sticky top-0 z-10 flex h-16 shrink-0 items-center justify-between border-b border-black/5 bg-white px-4 shadow-sm sm:px-6">
      <button
        type="button"
        onClick={onToggleSidebar}
        className="inline-flex items-center justify-center rounded-2xl p-2 text-black/60 transition hover:bg-black/5 hover:text-black lg:hidden"
      >
        <span className="sr-only">Open sidebar</span>
        <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
      </button>

      <div className="hidden lg:block">
        <p className="text-sm text-black/50">
          Welcome back, <span className="font-medium text-black">{user?.name || 'User'}</span>
        </p>
      </div>

      <div className="flex items-center gap-3">
        <span className={badgeClass}>{roleText}</span>

        <button
          type="button"
          onClick={handleLogout}
          disabled={loggingOut}
          className="rounded-2xl px-3 py-1.5 text-sm font-medium text-black/80 ring-1 ring-black/10 transition hover:bg-black/5 disabled:opacity-60"
        >
          {loggingOut ? 'Logging out...' : 'Log Out'}
        </button>
      </div>
    </header>
  )
}
