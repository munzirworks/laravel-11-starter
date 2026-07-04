import React, { useEffect, useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import apiClient from '../../api/client'
import DashboardLayout from '../../components/layout/DashboardLayout'
import { useAuth } from '../../context/AuthContext'

function formatAmount(value) {
  const numericValue = Number(value ?? 0)
  if (Number.isNaN(numericValue)) {
    return '0.00'
  }

  return numericValue.toLocaleString(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

export default function CustomerList() {
  const { can } = useAuth()
  const canView = can('customer.view')
  const canCreate = can('customer.create')
  const canUpdate = can('customer.update')

  const [customers, setCustomers] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [forbidden, setForbidden] = useState(!canView)
  const [pagination, setPagination] = useState({
    currentPage: 1,
    lastPage: 1,
    from: 0,
    to: 0,
    total: 0,
  })

  useEffect(() => {
    if (!canView) {
      setForbidden(true)
      setLoading(false)
      return
    }

    let isMounted = true

    async function loadCustomers(page = 1) {
      setLoading(true)
      setError('')
      setForbidden(false)

      try {
        const response = await apiClient.get('/customers', {
          params: { page },
        })

        if (!isMounted) {
          return
        }

        const payload = response.data?.data || {}
        setCustomers(Array.isArray(payload.data) ? payload.data : [])
        setPagination({
          currentPage: payload.current_page || 1,
          lastPage: payload.last_page || 1,
          from: payload.from || 0,
          to: payload.to || 0,
          total: payload.total || 0,
        })
      } catch (err) {
        if (!isMounted) {
          return
        }

        if (err.response?.status === 403) {
          setForbidden(true)
        } else {
          setError('Unable to load customers right now. Please try again.')
        }
      } finally {
        if (isMounted) {
          setLoading(false)
        }
      }
    }

    loadCustomers(1)

    return () => {
      isMounted = false
    }
  }, [canView])

  const pageLabel = useMemo(() => {
    if (!pagination.total) {
      return 'No records'
    }

    return `Showing ${pagination.from}-${pagination.to} of ${pagination.total}`
  }, [pagination])

  async function handlePageChange(nextPage) {
    if (!canView || nextPage < 1 || nextPage > pagination.lastPage || nextPage === pagination.currentPage) {
      return
    }

    setLoading(true)
    setError('')

    try {
      const response = await apiClient.get('/customers', {
        params: { page: nextPage },
      })
      const payload = response.data?.data || {}
      setCustomers(Array.isArray(payload.data) ? payload.data : [])
      setPagination({
        currentPage: payload.current_page || 1,
        lastPage: payload.last_page || 1,
        from: payload.from || 0,
        to: payload.to || 0,
        total: payload.total || 0,
      })
    } catch (err) {
      if (err.response?.status === 403) {
        setForbidden(true)
      } else {
        setError('Unable to change page right now. Please try again.')
      }
    } finally {
      setLoading(false)
    }
  }

  return (
    <DashboardLayout>
      <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h1 className="text-2xl font-semibold text-black">Customers</h1>
          <p className="mt-1 text-sm text-black/60">Manage customer master data and credit terms.</p>
        </div>

        {canCreate && (
          <Link to="/customers/create" className="inline-flex items-center rounded-2xl bg-black px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-900">
            New Customer
          </Link>
        )}
      </div>

      {forbidden && (
        <div className="rounded-3xl border border-black/5 bg-white p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-black">You don&apos;t have permission to view this page.</h2>
          <p className="mt-2 text-sm text-black/60">Contact your administrator if you need access to customer records.</p>
        </div>
      )}

      {!forbidden && loading && (
        <div className="rounded-3xl border border-black/5 bg-white p-6 text-sm text-black/60 shadow-sm">Loading customers...</div>
      )}

      {!forbidden && !loading && error && (
        <div className="rounded-3xl border border-red-200 bg-red-50 p-6 text-sm text-red-700 shadow-sm">{error}</div>
      )}

      {!forbidden && !loading && !error && (
        <div className="overflow-hidden rounded-3xl border border-black/5 bg-white shadow-sm">
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-black/5">
              <thead className="bg-slate-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-black/50">Code</th>
                  <th className="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-black/50">Name</th>
                  <th className="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-black/50">Contact</th>
                  <th className="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-black/50">Credit Limit</th>
                  <th className="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-black/50">Status</th>
                  <th className="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-black/50">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-black/5">
                {customers.map((customer) => (
                  <tr key={customer.id}>
                    <td className="px-6 py-4 text-sm font-medium text-black">{customer.code}</td>
                    <td className="px-6 py-4 text-sm text-black">{customer.name}</td>
                    <td className="px-6 py-4 text-sm text-black/60">{customer.email || customer.phone || '-'}</td>
                    <td className="px-6 py-4 text-sm text-black/60">{formatAmount(customer.credit_limit)}</td>
                    <td className="px-6 py-4 text-sm text-black/60">
                      <span className="inline-flex rounded-full bg-black/5 px-2.5 py-0.5 text-xs font-medium text-black/70">{customer.status || 'active'}</span>
                    </td>
                    <td className="px-6 py-4 text-right text-sm">
                      <div className="inline-flex items-center gap-2">
                        <Link to={`/customers/${customer.id}`} className="rounded-2xl px-3 py-1.5 text-black/70 ring-1 ring-black/10 transition hover:bg-black/5">
                          View
                        </Link>
                        {canUpdate && (
                          <Link to={`/customers/${customer.id}/edit`} className="rounded-2xl bg-[#FF2D20] px-3 py-1.5 text-white transition hover:brightness-95">
                            Edit
                          </Link>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}

                {customers.length === 0 && (
                  <tr>
                    <td colSpan={6} className="px-6 py-8 text-center text-sm text-black/50">
                      No customers found.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>

          <div className="flex flex-wrap items-center justify-between gap-3 border-t border-black/5 px-6 py-4">
            <p className="text-sm text-black/50">{pageLabel}</p>
            <div className="inline-flex items-center gap-2">
              <button
                type="button"
                onClick={() => handlePageChange(pagination.currentPage - 1)}
                disabled={pagination.currentPage <= 1}
                className="rounded-2xl px-3 py-1.5 text-sm text-black/80 ring-1 ring-black/10 transition hover:bg-black/5 disabled:opacity-50"
              >
                Previous
              </button>
              <span className="text-sm text-black/60">
                Page {pagination.currentPage} of {pagination.lastPage}
              </span>
              <button
                type="button"
                onClick={() => handlePageChange(pagination.currentPage + 1)}
                disabled={pagination.currentPage >= pagination.lastPage}
                className="rounded-2xl px-3 py-1.5 text-sm text-black/80 ring-1 ring-black/10 transition hover:bg-black/5 disabled:opacity-50"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      )}
    </DashboardLayout>
  )
}
