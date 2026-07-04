import React, { useEffect, useMemo, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
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

export default function CustomerDetail() {
  const { id } = useParams()
  const { can } = useAuth()
  const canView = can('customer.view')
  const canUpdate = can('customer.update')

  const [customer, setCustomer] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [forbidden, setForbidden] = useState(!canView)
  const [forbiddenMessage, setForbiddenMessage] = useState("You don't have permission to view this page.")

  useEffect(() => {
    if (!canView) {
      setForbidden(true)
      setForbiddenMessage("You don't have permission to view this page.")
      setLoading(false)
      return
    }

    let isMounted = true

    async function loadCustomer() {
      setLoading(true)
      setError('')
      setForbidden(false)

      try {
        const response = await apiClient.get(`/customers/${id}`)

        if (!isMounted) {
          return
        }

        setCustomer(response.data?.data || null)
      } catch (err) {
        if (!isMounted) {
          return
        }

        if (err.response?.status === 403) {
          setForbidden(true)
          setForbiddenMessage("You don't have access to this customer.")
        } else {
          setError('Unable to load customer details right now. Please try again.')
        }
      } finally {
        if (isMounted) {
          setLoading(false)
        }
      }
    }

    loadCustomer()

    return () => {
      isMounted = false
    }
  }, [canView, id])

  const detailRows = useMemo(() => {
    if (!customer) {
      return []
    }

    return [
      { label: 'Code', value: customer.code || '-' },
      { label: 'Name', value: customer.name || '-' },
      { label: 'Phone', value: customer.phone || '-' },
      { label: 'Email', value: customer.email || '-' },
      { label: 'Area', value: customer.area || '-' },
      { label: 'Address', value: customer.address || '-' },
      { label: 'Credit Limit', value: formatAmount(customer.credit_limit) },
      { label: 'Status', value: customer.status || '-' },
      { label: 'Remarks', value: customer.remarks || '-' },
      { label: 'Created By', value: customer.created_by || '-' },
      { label: 'Created At', value: customer.created_at ? new Date(customer.created_at).toLocaleString() : '-' },
      { label: 'Updated At', value: customer.updated_at ? new Date(customer.updated_at).toLocaleString() : '-' },
    ]
  }, [customer])

  return (
    <DashboardLayout>
      <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h1 className="text-2xl font-semibold text-black">Customer Details</h1>
          <p className="mt-1 text-sm text-black/60">Review customer information and status.</p>
        </div>

        <div className="flex items-center gap-2">
          <Link to="/customers" className="rounded-2xl px-3 py-1.5 text-sm text-black/80 ring-1 ring-black/10 transition hover:bg-black/5">
            Back to List
          </Link>

          {canUpdate && (
            <Link to={`/customers/${id}/edit`} className="rounded-2xl bg-black px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-900">
              Edit
            </Link>
          )}
        </div>
      </div>

      {forbidden && (
        <div className="rounded-3xl border border-black/5 bg-white p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-black">{forbiddenMessage}</h2>
          <p className="mt-2 text-sm text-black/60">Contact your administrator if you need customer access.</p>
        </div>
      )}

      {!forbidden && loading && <div className="rounded-3xl border border-black/5 bg-white p-6 text-sm text-black/60 shadow-sm">Loading customer details...</div>}

      {!forbidden && !loading && error && <div className="rounded-3xl border border-red-200 bg-red-50 p-6 text-sm text-red-700 shadow-sm">{error}</div>}

      {!forbidden && !loading && !error && customer && (
        <div className="rounded-3xl border border-black/5 bg-white shadow-sm">
          <div className="grid gap-0 divide-y divide-black/5">
            {detailRows.map((row) => (
              <div key={row.label} className="grid gap-2 px-6 py-4 md:grid-cols-3 md:gap-6">
                <p className="text-sm font-medium text-black/60">{row.label}</p>
                <p className="text-sm text-black md:col-span-2">{row.value}</p>
              </div>
            ))}
          </div>
        </div>
      )}
    </DashboardLayout>
  )
}
