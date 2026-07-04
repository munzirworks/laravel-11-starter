import React from 'react'
import { useAuth } from '../../context/AuthContext'

function navLinkClass(active) {
  return `flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-medium transition ${
    active ? 'bg-[#FF2D20] text-white shadow-sm' : 'text-zinc-300 hover:bg-white/10 hover:text-white'
  }`
}

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

function roleLabel(role) {
  if (role === 'admin') {
    return 'Administrator'
  }

  if (role === 'staff') {
    return 'Staff'
  }

  return role || 'User'
}

function HomeIcon() {
  return (
    <svg className="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" aria-hidden="true">
      <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
    </svg>
  )
}

function UserIcon() {
  return (
    <svg className="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" aria-hidden="true">
      <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
    </svg>
  )
}

function UsersIcon() {
  return (
    <svg className="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" aria-hidden="true">
      <path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
    </svg>
  )
}

function BuildingStorefrontIcon() {
  return (
    <svg className="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" aria-hidden="true">
      <path
        strokeLinecap="round"
        strokeLinejoin="round"
        d="M3 9.75L4.5 4.5h15L21 9.75m-18 0A2.25 2.25 0 005.25 12h.75A2.25 2.25 0 008.25 9.75m-5.25 0V19.5A1.5 1.5 0 004.5 21h15a1.5 1.5 0 001.5-1.5V9.75m-12.75 0A2.25 2.25 0 0010.5 12h3A2.25 2.25 0 0015.75 9.75m-7.5 0A2.25 2.25 0 0010.5 12m5.25-2.25A2.25 2.25 0 0018 12h.75A2.25 2.25 0 0021 9.75"
      />
    </svg>
  )
}

function CubeIcon() {
  return (
    <svg className="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" aria-hidden="true">
      <path
        strokeLinecap="round"
        strokeLinejoin="round"
        d="M21 7.5L12 3 3 7.5m18 0L12 12m9-4.5v9L12 21m9-4.5L12 12m0 9L3 16.5m9 4.5v-9m0 0L3 7.5"
      />
    </svg>
  )
}

export default function Sidebar({ isOpen, onClose }) {
  const { user, can } = useAuth()
  const role = roleFromUser(user)
  const isAdmin = role === 'admin'
  const canViewCustomers = can('customer.view')
  const canViewProducts = can('product.view')
  const pathname = window.location.pathname
  const initial = user?.name?.trim()?.charAt(0)?.toUpperCase() || '?'

  function handleNavClick() {
    if (window.innerWidth < 1024) {
      onClose()
    }
  }

  return (
    <aside
      className={`fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 flex-col border-r border-white/10 bg-black text-zinc-100 shadow-[0_20px_80px_rgba(15,23,42,0.25)] transition-transform duration-200 ease-in-out lg:static lg:z-auto lg:translate-x-0 lg:shadow-none ${
        isOpen ? 'translate-x-0' : '-translate-x-full lg:!translate-x-0'
      }`}
    >
      <div className="flex h-16 shrink-0 items-center gap-3 border-b border-white/10 px-6">
        <div className="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#FF2D20] text-base font-bold text-white">E</div>
        <span className="text-lg font-semibold tracking-tight text-white">ERP Core</span>
      </div>

      <nav className="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <p className="px-4 pb-2 text-xs font-semibold uppercase tracking-wider text-zinc-500">Main</p>

        <a href="/dashboard" onClick={handleNavClick} className={navLinkClass(pathname === '/dashboard')}>
          <HomeIcon />
          <span>Dashboard</span>
        </a>

        <a href="/profile" onClick={handleNavClick} className={navLinkClass(pathname.startsWith('/profile'))}>
          <UserIcon />
          <span>Profile</span>
        </a>

        {canViewCustomers && (
          <a href="/customers" onClick={handleNavClick} className={navLinkClass(pathname === '/customers' || pathname.startsWith('/customers/'))}>
            <BuildingStorefrontIcon />
            <span>Customers</span>
          </a>
        )}

        {canViewProducts && (
          <a href="/products" onClick={handleNavClick} className={navLinkClass(pathname === '/products' || pathname.startsWith('/products/'))}>
            <CubeIcon />
            <span>Products</span>
          </a>
        )}

        {isAdmin && (
          <>
            <p className="px-4 pb-2 pt-4 text-xs font-semibold uppercase tracking-wider text-zinc-500">Administration</p>

            <a href="/admin/users" onClick={handleNavClick} className={navLinkClass(pathname.startsWith('/admin'))}>
              <UsersIcon />
              <span>Users</span>
            </a>
          </>
        )}
      </nav>

      <div className="mt-auto shrink-0 border-t border-white/10 p-4">
        <div className="flex items-center gap-3 rounded-[24px] bg-white/5 p-3 ring-1 ring-white/10 backdrop-blur-sm">
          <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-zinc-800 text-sm font-semibold text-white ring-1 ring-white/10">
            {initial}
          </div>
          <div className="min-w-0 flex-1">
            <p className="truncate text-sm font-medium text-white">{user?.name || 'Unknown User'}</p>
            <p className="truncate text-xs text-white/60">{roleLabel(role)}</p>
          </div>
        </div>
      </div>
    </aside>
  )
}
