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

export default function ProductDetail() {
  const { id } = useParams()
  const { can } = useAuth()
  const canView = can('product.view')
  const canUpdate = can('product.update')

  const [product, setProduct] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [forbidden, setForbidden] = useState(!canView)

  useEffect(() => {
    if (!canView) {
      setForbidden(true)
      setLoading(false)
      return
    }

    let isMounted = true

    async function loadProduct() {
      setLoading(true)
      setError('')
      setForbidden(false)

      try {
        const response = await apiClient.get(`/products/${id}`)

        if (!isMounted) {
          return
        }

        setProduct(response.data?.data || null)
      } catch (err) {
        if (!isMounted) {
          return
        }

        if (err.response?.status === 403) {
          setForbidden(true)
        } else {
          setError('Unable to load product details right now. Please try again.')
        }
      } finally {
        if (isMounted) {
          setLoading(false)
        }
      }
    }

    loadProduct()

    return () => {
      isMounted = false
    }
  }, [canView, id])

  const detailRows = useMemo(() => {
    if (!product) {
      return []
    }

    return [
      { label: 'Code', value: product.code || '-' },
      { label: 'Name', value: product.name || '-' },
      { label: 'Category', value: product.category || '-' },
      { label: 'Barcode', value: product.barcode || '-' },
      { label: 'UOM', value: product.uom || 'PCS' },
      { label: 'Price', value: formatAmount(product.price) },
      { label: 'Cost', value: formatAmount(product.cost) },
      { label: 'Status', value: product.status || '-' },
      { label: 'Description', value: product.description || '-' },
      { label: 'Remarks', value: product.remarks || '-' },
      { label: 'Created By', value: product.created_by || '-' },
      { label: 'Created At', value: product.created_at ? new Date(product.created_at).toLocaleString() : '-' },
      { label: 'Updated At', value: product.updated_at ? new Date(product.updated_at).toLocaleString() : '-' },
    ]
  }, [product])

  return (
    <DashboardLayout>
      <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h1 className="text-2xl font-semibold text-black">Product Details</h1>
          <p className="mt-1 text-sm text-black/60">Review product information from the shared catalog.</p>
        </div>

        <div className="flex items-center gap-2">
          <Link to="/products" className="rounded-2xl px-3 py-1.5 text-sm text-black/80 ring-1 ring-black/10 transition hover:bg-black/5">
            Back to List
          </Link>

          {canUpdate && (
            <Link to={`/products/${id}/edit`} className="rounded-2xl bg-black px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-900">
              Edit
            </Link>
          )}
        </div>
      </div>

      {forbidden && (
        <div className="rounded-3xl border border-black/5 bg-white p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-black">You don&apos;t have permission to view this page.</h2>
          <p className="mt-2 text-sm text-black/60">Contact your administrator if you need product access.</p>
        </div>
      )}

      {!forbidden && loading && <div className="rounded-3xl border border-black/5 bg-white p-6 text-sm text-black/60 shadow-sm">Loading product details...</div>}

      {!forbidden && !loading && error && <div className="rounded-3xl border border-red-200 bg-red-50 p-6 text-sm text-red-700 shadow-sm">{error}</div>}

      {!forbidden && !loading && !error && product && (
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
