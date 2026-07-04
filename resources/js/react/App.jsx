import React from 'react'
import { Route, Routes } from 'react-router-dom'
import Landing from './pages/Landing'
import Login from './pages/Login'
import Register from './pages/Register'
import Dashboard from './pages/Dashboard'
import CustomerList from './pages/customers/CustomerList'
import CustomerCreate from './pages/customers/CustomerCreate'
import CustomerDetail from './pages/customers/CustomerDetail'
import CustomerEdit from './pages/customers/CustomerEdit'

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<Landing />} />
      <Route path="/login" element={<Login />} />
      <Route path="/register" element={<Register />} />
      <Route path="/dashboard" element={<Dashboard />} />
      <Route path="/customers" element={<CustomerList />} />
      <Route path="/customers/create" element={<CustomerCreate />} />
      <Route path="/customers/:id" element={<CustomerDetail />} />
      <Route path="/customers/:id/edit" element={<CustomerEdit />} />
    </Routes>
  )
}
