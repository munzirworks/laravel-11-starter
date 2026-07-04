import React, { useEffect, useMemo, useState } from 'react'
import apiClient from '../api/client'
import DashboardLayout from '../components/layout/DashboardLayout'
import { useAuth } from '../context/AuthContext'

const FALLBACK_STATS = {
  total_customers: 0,
  total_products: 0,
  total_warehouses: 0,
  total_quotations: 0,
  total_sales_orders: 0,
  total_invoices: 0,
  total_payments: 0,
  total_invoice_amount: '0.00',
  total_paid_amount: '0.00',
  total_outstanding_amount: '0.00',
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

function isMoneyStat(key) {
  return key.endsWith('_amount')
}

function formatStatValue(key, value) {
  if (value === null || value === undefined || value === '') {
    return '0'
  }

  if (isMoneyStat(key)) {
    return String(value)
  }

  const numericValue = Number(value)
  if (!Number.isNaN(numericValue)) {
    return numericValue.toLocaleString()
  }

  return String(value)
}

export default function Dashboard() {
  const { user } = useAuth()
  const [stats, setStats] = useState(FALLBACK_STATS)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [sessionExpired, setSessionExpired] = useState(false)

  useEffect(() => {
    let isMounted = true

    async function loadDashboard() {
      setLoading(true)
      setError('')
      setSessionExpired(false)

      try {
        const response = await apiClient.get('/reports/dashboard')
        if (!isMounted) {
          return
        }

        const payload = response.data?.data || {}
        setStats({
          ...FALLBACK_STATS,
          ...payload,
        })
      } catch (err) {
        if (!isMounted) {
          return
        }

        if (err.response?.status === 401) {
          setSessionExpired(true)
          setError('Session expired, please log in again.')
        } else {
          setError('Unable to load dashboard data right now. Please try again.')
        }
      } finally {
        if (isMounted) {
          setLoading(false)
        }
      }
    }

    loadDashboard()

    return () => {
      isMounted = false
    }
  }, [])

  const statCards = useMemo(
    () => [
      { key: 'total_customers', label: 'Total Customers' },
      { key: 'total_products', label: 'Total Products' },
      { key: 'total_warehouses', label: 'Total Warehouses' },
      { key: 'total_quotations', label: 'Total Quotations' },
      { key: 'total_sales_orders', label: 'Total Sales Orders' },
      { key: 'total_invoices', label: 'Total Invoices' },
      { key: 'total_payments', label: 'Total Payments' },
      { key: 'total_invoice_amount', label: 'Total Invoice Amount' },
      { key: 'total_paid_amount', label: 'Total Paid Amount' },
      { key: 'total_outstanding_amount', label: 'Total Outstanding Amount' },
    ],
    []
  )

  const role = roleFromUser(user)
  const isAdmin = role === 'admin'

  return (
    <DashboardLayout>
      <div className="mb-6">
        <h1 className="text-2xl font-semibold text-black">Dashboard</h1>
        <p className="mt-1 text-sm text-black/60">Overview of your account.</p>
      </div>

      {loading && (
        <div className="rounded-3xl border border-black/5 bg-white p-6 text-sm text-black/60 shadow-sm">
          Loading dashboard data...
        </div>
      )}

      {!loading && error && (
        <div className="rounded-3xl border border-red-200 bg-red-50 p-6 text-sm text-red-700 shadow-sm">
          <p>{error}</p>
          {sessionExpired && (
            <a href="/login" className="mt-3 inline-flex items-center rounded-2xl bg-red-600 px-4 py-2 font-medium text-white transition hover:bg-red-700">
              Go to login
            </a>
          )}
        </div>
      )}

      {!loading && !error && (
        <>
          <div className="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {statCards.map((stat) => (
              <div key={stat.key} className="rounded-3xl border border-black/5 bg-white p-6 shadow-sm">
                <p className="text-sm font-medium text-black/50">{stat.label}</p>
                <p className="mt-2 text-xl font-semibold text-black">{formatStatValue(stat.key, stats[stat.key])}</p>
              </div>
            ))}
          </div>

          <div className="rounded-3xl border border-black/5 bg-white shadow-sm">
            <div className="border-b border-black/5 px-6 py-4">
              <h3 className="text-base font-semibold text-black">Welcome</h3>
            </div>
            <div className="p-6">
              <p className="text-sm text-black/60">
                You are signed in. This starter kit includes authentication, a dashboard layout, and role-based access for admin and staff users.
              </p>

              {isAdmin && (
                <div className="mt-4">
                  <a
                    href="/admin/users"
                    className="inline-flex items-center rounded-2xl bg-black px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-900"
                  >
                    Manage users
                  </a>
                </div>
              )}
            </div>
          </div>
        </>
      )}
    </DashboardLayout>
  )
}
