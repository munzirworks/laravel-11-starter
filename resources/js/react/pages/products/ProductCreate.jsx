import React, { useState } from 'react'
import { Link } from 'react-router-dom'
import apiClient from '../../api/client'
import DashboardLayout from '../../components/layout/DashboardLayout'
import { useAuth } from '../../context/AuthContext'

const INITIAL_FORM = {
  code: '',
  name: '',
  description: '',
  category: '',
  barcode: '',
  uom: 'PCS',
  price: '',
  cost: '',
  status: 'active',
  remarks: '',
}

export default function ProductCreate() {
  const { can } = useAuth()
  const canCreate = can('product.create')

  const [form, setForm] = useState(INITIAL_FORM)
  const [errors, setErrors] = useState({})
  const [error, setError] = useState('')
  const [submitting, setSubmitting] = useState(false)

  function handleChange(event) {
    const { name, value } = event.target
    setForm((prev) => ({
      ...prev,
      [name]: value,
    }))
  }

  async function handleSubmit(event) {
    event.preventDefault()

    setSubmitting(true)
    setError('')
    setErrors({})

    try {
      const payload = {
        ...form,
        price: form.price === '' ? null : form.price,
        cost: form.cost === '' ? null : form.cost,
      }

      const response = await apiClient.post('/products', payload)
      const productId = response.data?.data?.id

      window.location.href = productId ? `/products/${productId}` : '/products'
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {})
      } else if (err.response?.status === 403) {
        setError("You don't have permission to create products.")
      } else {
        setError('Unable to create product right now. Please try again.')
      }
    } finally {
      setSubmitting(false)
    }
  }

  if (!canCreate) {
    return (
      <DashboardLayout>
        <div className="rounded-3xl border border-black/5 bg-white p-6 shadow-sm">
          <h1 className="text-xl font-semibold text-black">You don&apos;t have permission to view this page.</h1>
          <p className="mt-2 text-sm text-black/60">Contact your administrator to request product create access.</p>
        </div>
      </DashboardLayout>
    )
  }

  return (
    <DashboardLayout>
      <div className="mb-6 flex items-center justify-between gap-3">
        <div>
          <h1 className="text-2xl font-semibold text-black">New Product</h1>
          <p className="mt-1 text-sm text-black/60">Add a product to the shared catalog.</p>
        </div>

        <Link to="/products" className="rounded-2xl px-3 py-1.5 text-sm text-black/80 ring-1 ring-black/10 transition hover:bg-black/5">
          Back to List
        </Link>
      </div>

      <div className="rounded-3xl border border-black/5 bg-white p-6 shadow-sm sm:p-8">
        {error && <div className="mb-6 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-100">{error}</div>}

        <form onSubmit={handleSubmit} className="space-y-5">
          <div className="grid gap-5 md:grid-cols-2">
            <div>
              <label htmlFor="code" className="block text-sm font-medium text-black">
                Product Code
              </label>
              <input
                id="code"
                name="code"
                value={form.code}
                onChange={handleChange}
                required
                maxLength={50}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.code && <p className="mt-1 text-xs text-red-600">{errors.code[0]}</p>}
            </div>

            <div>
              <label htmlFor="name" className="block text-sm font-medium text-black">
                Name
              </label>
              <input
                id="name"
                name="name"
                value={form.name}
                onChange={handleChange}
                required
                maxLength={255}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.name && <p className="mt-1 text-xs text-red-600">{errors.name[0]}</p>}
            </div>

            <div>
              <label htmlFor="category" className="block text-sm font-medium text-black">
                Category
              </label>
              <input
                id="category"
                name="category"
                value={form.category}
                onChange={handleChange}
                maxLength={100}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.category && <p className="mt-1 text-xs text-red-600">{errors.category[0]}</p>}
            </div>

            <div>
              <label htmlFor="barcode" className="block text-sm font-medium text-black">
                Barcode
              </label>
              <input
                id="barcode"
                name="barcode"
                value={form.barcode}
                onChange={handleChange}
                maxLength={100}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.barcode && <p className="mt-1 text-xs text-red-600">{errors.barcode[0]}</p>}
            </div>

            <div>
              <label htmlFor="uom" className="block text-sm font-medium text-black">
                UOM
              </label>
              <input
                id="uom"
                name="uom"
                value={form.uom}
                onChange={handleChange}
                maxLength={20}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.uom && <p className="mt-1 text-xs text-red-600">{errors.uom[0]}</p>}
            </div>

            <div>
              <label htmlFor="status" className="block text-sm font-medium text-black">
                Status
              </label>
              <input
                id="status"
                name="status"
                value={form.status}
                onChange={handleChange}
                maxLength={50}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.status && <p className="mt-1 text-xs text-red-600">{errors.status[0]}</p>}
            </div>

            <div>
              <label htmlFor="price" className="block text-sm font-medium text-black">
                Price
              </label>
              <input
                id="price"
                name="price"
                type="number"
                min="0"
                step="0.01"
                value={form.price}
                onChange={handleChange}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.price && <p className="mt-1 text-xs text-red-600">{errors.price[0]}</p>}
            </div>

            <div>
              <label htmlFor="cost" className="block text-sm font-medium text-black">
                Cost
              </label>
              <input
                id="cost"
                name="cost"
                type="number"
                min="0"
                step="0.01"
                value={form.cost}
                onChange={handleChange}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.cost && <p className="mt-1 text-xs text-red-600">{errors.cost[0]}</p>}
            </div>
          </div>

          <div>
            <label htmlFor="description" className="block text-sm font-medium text-black">
              Description
            </label>
            <textarea
              id="description"
              name="description"
              value={form.description}
              onChange={handleChange}
              rows={3}
              className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
            />
            {errors.description && <p className="mt-1 text-xs text-red-600">{errors.description[0]}</p>}
          </div>

          <div>
            <label htmlFor="remarks" className="block text-sm font-medium text-black">
              Remarks
            </label>
            <textarea
              id="remarks"
              name="remarks"
              value={form.remarks}
              onChange={handleChange}
              rows={3}
              className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
            />
            {errors.remarks && <p className="mt-1 text-xs text-red-600">{errors.remarks[0]}</p>}
          </div>

          <div className="flex flex-wrap items-center gap-3 pt-2">
            <button type="submit" disabled={submitting} className="rounded-2xl bg-black px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-900 disabled:opacity-60">
              {submitting ? 'Creating...' : 'Create Product'}
            </button>
            <Link to="/products" className="rounded-2xl px-4 py-2 text-sm font-medium text-black/80 ring-1 ring-black/10 transition hover:bg-black/5">
              Cancel
            </Link>
          </div>
        </form>
      </div>
    </DashboardLayout>
  )
}
