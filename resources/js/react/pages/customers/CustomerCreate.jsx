import React, { useState } from 'react'
import { Link } from 'react-router-dom'
import apiClient from '../../api/client'
import DashboardLayout from '../../components/layout/DashboardLayout'
import { useAuth } from '../../context/AuthContext'

const INITIAL_FORM = {
  code: '',
  name: '',
  phone: '',
  email: '',
  address: '',
  area: '',
  credit_limit: '',
  status: 'active',
  remarks: '',
}

export default function CustomerCreate() {
  const { can } = useAuth()
  const canCreate = can('customer.create')

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
        credit_limit: form.credit_limit === '' ? null : form.credit_limit,
      }

      const response = await apiClient.post('/customers', payload)
      const customerId = response.data?.data?.id

      window.location.href = customerId ? `/customers/${customerId}` : '/customers'
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {})
      } else if (err.response?.status === 403) {
        setError("You don't have permission to create customers.")
      } else {
        setError('Unable to create customer right now. Please try again.')
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
          <p className="mt-2 text-sm text-black/60">Contact your administrator to request customer create access.</p>
        </div>
      </DashboardLayout>
    )
  }

  return (
    <DashboardLayout>
      <div className="mb-6 flex items-center justify-between gap-3">
        <div>
          <h1 className="text-2xl font-semibold text-black">New Customer</h1>
          <p className="mt-1 text-sm text-black/60">Create a customer record for sales and invoicing.</p>
        </div>

        <Link to="/customers" className="rounded-2xl px-3 py-1.5 text-sm text-black/80 ring-1 ring-black/10 transition hover:bg-black/5">
          Back to List
        </Link>
      </div>

      <div className="rounded-3xl border border-black/5 bg-white p-6 shadow-sm sm:p-8">
        {error && <div className="mb-6 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-100">{error}</div>}

        <form onSubmit={handleSubmit} className="space-y-5">
          <div className="grid gap-5 md:grid-cols-2">
            <div>
              <label htmlFor="code" className="block text-sm font-medium text-black">
                Customer Code
              </label>
              <input
                id="code"
                name="code"
                value={form.code}
                onChange={handleChange}
                required
                maxLength={20}
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
              <label htmlFor="phone" className="block text-sm font-medium text-black">
                Phone
              </label>
              <input
                id="phone"
                name="phone"
                value={form.phone}
                onChange={handleChange}
                maxLength={20}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.phone && <p className="mt-1 text-xs text-red-600">{errors.phone[0]}</p>}
            </div>

            <div>
              <label htmlFor="email" className="block text-sm font-medium text-black">
                Email
              </label>
              <input
                id="email"
                name="email"
                type="email"
                value={form.email}
                onChange={handleChange}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.email && <p className="mt-1 text-xs text-red-600">{errors.email[0]}</p>}
            </div>

            <div>
              <label htmlFor="area" className="block text-sm font-medium text-black">
                Area
              </label>
              <input
                id="area"
                name="area"
                value={form.area}
                onChange={handleChange}
                maxLength={100}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.area && <p className="mt-1 text-xs text-red-600">{errors.area[0]}</p>}
            </div>

            <div>
              <label htmlFor="credit_limit" className="block text-sm font-medium text-black">
                Credit Limit
              </label>
              <input
                id="credit_limit"
                name="credit_limit"
                type="number"
                step="0.01"
                min="0"
                value={form.credit_limit}
                onChange={handleChange}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.credit_limit && <p className="mt-1 text-xs text-red-600">{errors.credit_limit[0]}</p>}
            </div>
          </div>

          <div>
            <label htmlFor="address" className="block text-sm font-medium text-black">
              Address
            </label>
            <textarea
              id="address"
              name="address"
              value={form.address}
              onChange={handleChange}
              rows={3}
              maxLength={500}
              className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
            />
            {errors.address && <p className="mt-1 text-xs text-red-600">{errors.address[0]}</p>}
          </div>

          <div className="grid gap-5 md:grid-cols-2">
            <div>
              <label htmlFor="status" className="block text-sm font-medium text-black">
                Status
              </label>
              <select
                id="status"
                name="status"
                value={form.status}
                onChange={handleChange}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              {errors.status && <p className="mt-1 text-xs text-red-600">{errors.status[0]}</p>}
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
                maxLength={1000}
                className="mt-1 w-full rounded-2xl border border-black/10 px-4 py-2.5 text-sm focus:border-[#FF2D20] focus:outline-none focus:ring-1 focus:ring-[#FF2D20]"
              />
              {errors.remarks && <p className="mt-1 text-xs text-red-600">{errors.remarks[0]}</p>}
            </div>
          </div>

          <div className="flex flex-wrap items-center gap-3 pt-2">
            <button type="submit" disabled={submitting} className="rounded-2xl bg-black px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-900 disabled:opacity-60">
              {submitting ? 'Creating...' : 'Create Customer'}
            </button>
            <Link to="/customers" className="rounded-2xl px-4 py-2 text-sm font-medium text-black/80 ring-1 ring-black/10 transition hover:bg-black/5">
              Cancel
            </Link>
          </div>
        </form>
      </div>
    </DashboardLayout>
  )
}
