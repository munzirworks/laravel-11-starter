import React, { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
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

export default function CustomerEdit() {
  const { id } = useParams()
  const { can } = useAuth()
  const canUpdate = can('customer.update')

  const [form, setForm] = useState(INITIAL_FORM)
  const [loading, setLoading] = useState(true)
  const [submitting, setSubmitting] = useState(false)
  const [errors, setErrors] = useState({})
  const [error, setError] = useState('')
  const [forbidden, setForbidden] = useState(!canUpdate)
  const [forbiddenMessage, setForbiddenMessage] = useState("You don't have permission to view this page.")

  useEffect(() => {
    if (!canUpdate) {
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

        const customer = response.data?.data || {}
        setForm({
          code: customer.code || '',
          name: customer.name || '',
          phone: customer.phone || '',
          email: customer.email || '',
          address: customer.address || '',
          area: customer.area || '',
          credit_limit: customer.credit_limit ?? '',
          status: customer.status || 'active',
          remarks: customer.remarks || '',
        })
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
  }, [canUpdate, id])

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

      await apiClient.put(`/customers/${id}`, payload)
      window.location.href = `/customers/${id}`
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {})
      } else if (err.response?.status === 403) {
        setForbidden(true)
        setForbiddenMessage("You don't have access to this customer.")
      } else {
        setError('Unable to update customer right now. Please try again.')
      }
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <DashboardLayout>
      <div className="mb-6 flex items-center justify-between gap-3">
        <div>
          <h1 className="text-2xl font-semibold text-black">Edit Customer</h1>
          <p className="mt-1 text-sm text-black/60">Update customer profile and credit settings.</p>
        </div>

        <Link to={`/customers/${id}`} className="rounded-2xl px-3 py-1.5 text-sm text-black/80 ring-1 ring-black/10 transition hover:bg-black/5">
          Back to Detail
        </Link>
      </div>

      {forbidden && (
        <div className="rounded-3xl border border-black/5 bg-white p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-black">{forbiddenMessage}</h2>
          <p className="mt-2 text-sm text-black/60">Contact your administrator to request customer access.</p>
        </div>
      )}

      {!forbidden && loading && <div className="rounded-3xl border border-black/5 bg-white p-6 text-sm text-black/60 shadow-sm">Loading customer data...</div>}

      {!forbidden && !loading && (
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
                {submitting ? 'Saving...' : 'Save Changes'}
              </button>
              <Link to={`/customers/${id}`} className="rounded-2xl px-4 py-2 text-sm font-medium text-black/80 ring-1 ring-black/10 transition hover:bg-black/5">
                Cancel
              </Link>
            </div>
          </form>
        </div>
      )}
    </DashboardLayout>
  )
}
