import React from 'react'

function Navbar() {
  return (
    <nav className="flex items-center justify-between py-5 border-b border-black/5 bg-white/90 backdrop-blur-sm">
      <div className="flex items-center gap-3">
        <div className="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#FF2D20] text-white font-bold">E</div>
        <div>
          <p className="text-base font-semibold">ERP Core</p>
          <p className="text-xs text-black/50">API-first ERP platform</p>
        </div>
      </div>

      <div className="flex items-center gap-3">
        <a href="/login" className="rounded-full px-4 py-2 text-sm font-medium text-black ring-1 ring-black/10 transition hover:bg-black/5">Login</a>
        <a href="/register" className="rounded-full bg-black px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-900">Register</a>
      </div>
    </nav>
  )
}

function DashboardPreview() {
  return (
    <div className="rounded-[30px] border border-black/5 bg-gradient-to-br from-white via-slate-50 to-slate-100 p-6 shadow-[0_20px_80px_rgba(15,23,42,0.08)]">
      <div className="flex items-start justify-between gap-4">
        <div>
          <p className="text-sm font-semibold uppercase tracking-[0.18em] text-[#FF2D20]">Dashboard preview</p>
          <h3 className="mt-4 text-2xl font-bold text-black">Live operational summary</h3>
        </div>
        <span className="rounded-full bg-black/10 px-3 py-1 text-xs font-semibold text-black/70">Beta</span>
      </div>

      <div className="mt-6 grid gap-4 sm:grid-cols-3">
        <div className="rounded-3xl bg-white p-4 shadow-sm border border-black/5">
          <p className="text-sm text-black/50">Open Orders</p>
          <p className="mt-2 text-2xl font-semibold">142</p>
        </div>
        <div className="rounded-3xl bg-white p-4 shadow-sm border border-black/5">
          <p className="text-sm text-black/50">Available Stock</p>
          <p className="mt-2 text-2xl font-semibold">5,980</p>
        </div>
        <div className="rounded-3xl bg-white p-4 shadow-sm border border-black/5">
          <p className="text-sm text-black/50">Payments Today</p>
          <p className="mt-2 text-2xl font-semibold">$37.2k</p>
        </div>
      </div>

      <div className="mt-6 rounded-3xl bg-black/5 p-4">
        <div className="mb-3 flex items-center justify-between text-sm text-black/60">
          <span>Sales trend</span>
          <span className="font-semibold text-black">+14%</span>
        </div>
        <div className="flex items-end gap-2 h-28">
          <div className="flex-1 rounded-full bg-black/10" style={{ height: '22px' }} />
          <div className="flex-1 rounded-full bg-black/20" style={{ height: '30px' }} />
          <div className="flex-1 rounded-full bg-[#FF2D20]" style={{ height: '38px' }} />
          <div className="flex-1 rounded-full bg-black/20" style={{ height: '25px' }} />
          <div className="flex-1 rounded-full bg-black/10" style={{ height: '18px' }} />
        </div>
      </div>
    </div>
  )
}

function ModuleCard({ title, description }) {
  return (
    <div className="group rounded-3xl border border-black/5 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
      <div className="flex items-center justify-between gap-3">
        <div className="h-10 w-10 rounded-2xl bg-[#FF2D20]/10 text-[#FF2D20] grid place-items-center font-bold">•</div>
        <span className="text-xs font-semibold uppercase tracking-[0.18em] text-black/50">Module</span>
      </div>
      <h4 className="mt-6 text-lg font-semibold text-black">{title}</h4>
      <p className="mt-3 text-sm leading-6 text-black/60">{description}</p>
    </div>
  )
}

export default function App() {
  const modules = [
    {
      title: 'Customer Management',
      description: 'Track accounts, contacts, interactions and customer history in one place.',
    },
    {
      title: 'Product Management',
      description: 'Manage product catalogs, pricing, variants and stock availability.',
    },
    {
      title: 'Sales Order',
      description: 'Create, approve and fulfil sales orders from a single workflow.',
    },
    {
      title: 'Inventory',
      description: 'Monitor stock movements, warehouses, and reorder levels in real time.',
    },
    {
      title: 'Quotation',
      description: 'Build professional quotes quickly and convert them to orders.',
    },
    {
      title: 'Invoice & Payment',
      description: 'Issue invoices, track payments and reconcile collections efficiently.',
    },
    {
      title: 'Reporting',
      description: 'Generate sales, stock and financial reports for faster decisions.',
    },
    {
      title: 'Roles & Permissions',
      description: 'Control access for teams, agents and administrators securely.',
    },
  ]

  return (
    <div className="min-h-screen bg-slate-50 text-black">
      <div className="mx-auto max-w-7xl px-6 py-6">
        <Navbar />

        <main className="mt-10 space-y-12">
          <section className="grid gap-8 lg:grid-cols-[1.3fr_0.9fr] items-start">
            <div className="space-y-6">
              <span className="inline-flex rounded-full bg-[#FF2D20]/10 px-4 py-1 text-sm font-semibold text-[#FF2D20]">API-first ERP for modern teams</span>
              <div className="space-y-5">
                <h1 className="max-w-3xl text-5xl font-extrabold tracking-tight text-slate-950">ERP Core</h1>
                <p className="max-w-2xl text-lg leading-8 text-slate-700">API-first ERP platform for sales, inventory, invoicing, reporting and mobile agent workflow.</p>
              </div>

              <div className="flex flex-wrap gap-3">
                <a href="/login" className="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 hover:bg-slate-100">Login</a>
                <a href="/register" className="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">Create Account</a>
                <a href="/dashboard" className="inline-flex items-center justify-center rounded-full bg-[#FF2D20] px-5 py-3 text-sm font-semibold text-white hover:bg-[#e02b20]">View Dashboard</a>
              </div>

              <div className="grid gap-4 sm:grid-cols-2">
                <div className="rounded-3xl bg-white p-5 shadow-sm border border-black/5">
                  <p className="text-sm font-semibold text-slate-900">Trusted by growing businesses</p>
                  <p className="mt-2 text-sm text-slate-600">Scale operations across sales, inventory, and finance with a single API-powered backend.</p>
                </div>
                <div className="rounded-3xl bg-white p-5 shadow-sm border border-black/5">
                  <p className="text-sm font-semibold text-slate-900">Ready for the next phase</p>
                  <p className="mt-2 text-sm text-slate-600">A clean React landing page sits on top of your existing Laravel API and Breeze auth.</p>
                </div>
              </div>
            </div>

            <DashboardPreview />
          </section>

          <section className="rounded-[32px] bg-white p-8 shadow-sm border border-black/5">
            <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <p className="text-sm font-semibold uppercase tracking-[0.18em] text-black/50">Core modules</p>
                <h2 className="mt-3 text-3xl font-bold text-slate-950">Business modules built for ERP workflows</h2>
              </div>
              <p className="max-w-xl text-sm leading-6 text-slate-600">Everything needed to manage customers, products, orders, inventory, invoicing and access control in one place.</p>
            </div>

            <div className="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
              {modules.map((module) => (
                <ModuleCard key={module.title} title={module.title} description={module.description} />
              ))}
            </div>
          </section>

          <section className="rounded-[32px] bg-gradient-to-r from-white via-slate-100 to-slate-50 p-8 shadow-sm border border-black/5">
            <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
              <div>
                <p className="text-sm font-semibold uppercase tracking-[0.18em] text-black/50">Business flow</p>
                <h2 className="mt-3 text-3xl font-bold text-slate-950">From customer to payment in one flow</h2>
              </div>
              <p className="max-w-xl text-sm leading-6 text-slate-600">A clear, linear process helps teams move faster and keep every order, invoice, and payment aligned.</p>
            </div>

            <div className="mt-8 grid gap-4 sm:grid-cols-5 sm:items-center">
              {['Customer', 'Quotation', 'Sales Order', 'Invoice', 'Payment'].map((step, index) => (
                <div key={step} className="flex flex-col items-center gap-3 rounded-3xl bg-white p-5 text-center shadow-sm border border-black/5">
                  <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#FF2D20]/10 text-[#FF2D20] font-semibold">{index + 1}</div>
                  <p className="font-semibold text-slate-900">{step}</p>
                </div>
              ))}
            </div>
          </section>
        </main>

        <footer className="mt-12 py-6 text-center text-sm text-slate-500">
          ERP Core Backend + React Admin + Mobile Agent App
        </footer>
      </div>
    </div>
  )
}
